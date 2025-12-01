<?php

namespace App\Services\Integrations;

use App\Models\IntegrationEvent;
use App\Models\IntegrationHealthSnapshot;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class IntegrationHealthService
{
    public function __construct(
        protected IntegrationTelemetryClient $telemetryClient,
        protected CacheRepository $cache
    ) {
    }

    public function getDashboardPayload(?string $region, ?string $integrationType): array
    {
        $this->syncFromTelemetry($region, $integrationType);

        $snapshots = IntegrationHealthSnapshot::query()
            ->forRegion($region)
            ->forIntegrationType($integrationType)
            ->orderBy('display_order')
            ->orderBy('integration_slug')
            ->get();

        return [
            'snapshots' => $snapshots,
            'heroMetrics' => $this->buildHeroMetrics($snapshots),
            'timeline' => $this->fetchTimeline($region, $integrationType),
            'nextActions' => $this->buildNextActions($snapshots),
            'filters' => $this->buildFilterOptions(),
        ];
    }

    protected function syncFromTelemetry(?string $region, ?string $integrationType): void
    {
        $cacheKey = $this->buildCacheKey($region, $integrationType);

        if ($this->cache->has($cacheKey)) {
            return;
        }

        $payload = $this->telemetryClient->pullTelemetry($region, $integrationType);

        if (! $payload) {
            return;
        }

        DB::transaction(function () use ($payload) {
            $this->persistSnapshots($payload['integrations'] ?? []);
            $this->persistEvents($payload['events'] ?? []);
        });

        $ttl = config('integration-health.telemetry.cache_ttl', 60);
        $this->cache->put($cacheKey, true, now()->addSeconds($ttl));
    }

    protected function persistSnapshots(array $integrations): void
    {
        if (empty($integrations)) {
            return;
        }

        $rows = collect($integrations)
            ->map(function (array $snapshot) {
                $now = now();

                return [
                    'integration_slug' => Arr::get($snapshot, 'slug'),
                    'display_name' => Arr::get($snapshot, 'name', Arr::get($snapshot, 'slug', '')),
                    'vendor' => Arr::get($snapshot, 'vendor'),
                    'integration_type' => Arr::get($snapshot, 'type'),
                    'region' => Arr::get($snapshot, 'region'),
                    'environment' => Arr::get($snapshot, 'environment', 'production'),
                    'status' => Arr::get($snapshot, 'status', 'unknown'),
                    'status_message' => Arr::get($snapshot, 'message'),
                    'latency_ms' => Arr::get($snapshot, 'latency_ms'),
                    'last_synced_at' => Arr::get($snapshot, 'last_synced_at')
                        ? Carbon::parse(Arr::get($snapshot, 'last_synced_at'))
                        : $now,
                    'throughput_per_minute' => Arr::get($snapshot, 'throughput_per_minute', 0),
                    'error_rate' => Arr::get($snapshot, 'error_rate', 0),
                    'uptime_percentage' => Arr::get($snapshot, 'uptime_percentage', 0),
                    'active_automations' => Arr::get($snapshot, 'active_automations', 0),
                    'channels' => json_encode(Arr::get($snapshot, 'channels', [])),
                    'metadata' => json_encode(Arr::get($snapshot, 'metadata', [])),
                    'source' => Arr::get($snapshot, 'source', 'api'),
                    'display_order' => Arr::get($snapshot, 'display_order', 0),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->filter(fn ($row) => filled($row['integration_slug']))
            ->values()
            ->all();

        if (empty($rows)) {
            return;
        }

        IntegrationHealthSnapshot::upsert(
            $rows,
            ['integration_slug', 'region', 'environment'],
            [
                'display_name',
                'vendor',
                'integration_type',
                'status',
                'status_message',
                'latency_ms',
                'last_synced_at',
                'throughput_per_minute',
                'error_rate',
                'uptime_percentage',
                'active_automations',
                'channels',
                'metadata',
                'source',
                'display_order',
                'updated_at',
            ]
        );
    }

    protected function persistEvents(array $events): void
    {
        if (empty($events)) {
            return;
        }

        foreach ($events as $event) {
            $occurredAt = Arr::get($event, 'occurred_at')
                ? Carbon::parse(Arr::get($event, 'occurred_at'))
                : now();

            IntegrationEvent::updateOrCreate(
                [
                    'integration_slug' => Arr::get($event, 'slug'),
                    'title' => Arr::get($event, 'title'),
                    'occurred_at' => $occurredAt,
                ],
                [
                    'region' => Arr::get($event, 'region'),
                    'integration_type' => Arr::get($event, 'type'),
                    'severity' => Arr::get($event, 'severity', 'info'),
                    'detail' => Arr::get($event, 'detail'),
                    'meta' => json_encode(Arr::get($event, 'meta', [])),
                ]
            );
        }
    }

    protected function fetchTimeline(?string $region, ?string $integrationType): Collection
    {
        return IntegrationEvent::query()
            ->forRegion($region)
            ->forIntegrationType($integrationType)
            ->latest('occurred_at')
            ->limit(12)
            ->get();
    }

    protected function buildHeroMetrics(Collection $snapshots): array
    {
        $connectedServices = $snapshots->count();
        $activeAutomations = (int) $snapshots->sum('active_automations');
        $averageReliability = $snapshots->avg(function ($snapshot) {
            return max(0, 100 - (float) $snapshot->error_rate);
        }) ?? 100;

        $uptime = $snapshots->avg('uptime_percentage');
        $uptimeText = $uptime
            ? number_format($uptime, 2) . '% global uptime'
            : 'Live telemetry on standby';

        $uptimeVariant = $uptime >= 99.9 ? 'success' : ($uptime >= 99 ? 'warning' : 'danger');

        return [
            'connected_services' => $connectedServices,
            'active_automations' => $activeAutomations,
            'data_reliability' => number_format($averageReliability, 2),
            'uptime_badge_text' => $uptimeText,
            'uptime_badge_variant' => $uptimeVariant,
            'compliance_summary' => 'SOC 2 • GDPR • ISO 27001 alignment confirmed',
        ];
    }

    protected function buildNextActions(Collection $snapshots): array
    {
        $actions = [];

        $degraded = $snapshots->where('status', 'degraded');
        if ($degraded->isNotEmpty()) {
            $actions[] = sprintf(
                '%d connectors reporting degraded performance — confirm vendor SLAs and open proactive tickets.',
                $degraded->count()
            );
        }

        $highLatency = $snapshots->filter(fn ($snapshot) => $snapshot->latency_ms && $snapshot->latency_ms > 400);
        if ($highLatency->isNotEmpty()) {
            $actions[] = sprintf(
                '%d integrations above 400ms latency — switch affected regions to warm standby until normalized.',
                $highLatency->count()
            );
        }

        $staleSyncs = $snapshots->filter(function ($snapshot) {
            return $snapshot->last_synced_at && $snapshot->last_synced_at->lt(now()->subMinutes(30));
        });
        if ($staleSyncs->isNotEmpty()) {
            $actions[] = sprintf(
                'Audit %d connectors with stale syncs (>30m) and trigger manual reconciliation.',
                $staleSyncs->count()
            );
        }

        if (empty($actions)) {
            $actions[] = 'Maintain weekly tabletop drills for cross-region failover + webhook replay. Next review due Friday.';
            $actions[] = 'Update integration playbooks with the latest vendor contact ladder and rollback steps.';
        }

        return array_slice($actions, 0, 4);
    }

    protected function buildFilterOptions(): array
    {
        return [
            'regions' => IntegrationHealthSnapshot::query()
                ->whereNotNull('region')
                ->select('region')
                ->distinct()
                ->pluck('region')
                ->sort()
                ->values()
                ->all(),
            'types' => IntegrationHealthSnapshot::query()
                ->whereNotNull('integration_type')
                ->select('integration_type')
                ->distinct()
                ->pluck('integration_type')
                ->sort()
                ->values()
                ->all(),
        ];
    }

    protected function buildCacheKey(?string $region, ?string $integrationType): string
    {
        return 'integration-health:sync:' . md5(($region ?? 'all') . '|' . ($integrationType ?? 'all'));
    }
}
