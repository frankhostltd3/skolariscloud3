<?php

namespace Database\Seeders;

use App\Models\IntegrationEvent;
use App\Models\IntegrationHealthSnapshot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class IntegrationHealthDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (IntegrationHealthSnapshot::exists()) {
            return;
        }

        $now = now();

        IntegrationHealthSnapshot::query()->insert([
            [
                'integration_slug' => 'finance-erp',
                'display_name' => 'Finance ERP',
                'vendor' => 'SAP S/4HANA',
                'integration_type' => 'finance',
                'region' => 'emea',
                'environment' => 'production',
                'status' => 'live',
                'latency_ms' => 245,
                'last_synced_at' => $now->copy()->subMinutes(2),
                'throughput_per_minute' => 180,
                'error_rate' => 0.4,
                'uptime_percentage' => 99.98,
                'active_automations' => 42,
                'channels' => json_encode(['Payments', 'Invoices', 'Expenses']),
                'metadata' => json_encode(['uptime' => 99.98]),
                'source' => 'seed',
                'display_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'integration_slug' => 'messaging-stack',
                'display_name' => 'Messaging Stack',
                'vendor' => 'Twilio + Meta API',
                'integration_type' => 'communication',
                'region' => 'emea',
                'environment' => 'production',
                'status' => 'degraded',
                'latency_ms' => 410,
                'last_synced_at' => $now->copy()->subMinutes(7),
                'throughput_per_minute' => 980,
                'error_rate' => 1.7,
                'uptime_percentage' => 99.52,
                'active_automations' => 33,
                'channels' => json_encode(['SMS', 'WhatsApp', 'Voice']),
                'metadata' => json_encode(['uptime' => 99.52]),
                'source' => 'seed',
                'display_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'integration_slug' => 'data-warehouse',
                'display_name' => 'Data Warehouse',
                'vendor' => 'Snowflake',
                'integration_type' => 'analytics',
                'region' => 'amer',
                'environment' => 'production',
                'status' => 'scheduled',
                'latency_ms' => null,
                'last_synced_at' => $now->copy()->subHours(4),
                'throughput_per_minute' => 45,
                'error_rate' => 0.1,
                'uptime_percentage' => 99.9,
                'active_automations' => 18,
                'channels' => json_encode(['Finance KPIs', 'Attendance', 'Behavior']),
                'metadata' => json_encode(['next_run' => '01:00 UTC']),
                'source' => 'seed',
                'display_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'integration_slug' => 'learning-tools',
                'display_name' => 'Learning Tools',
                'vendor' => 'Google Workspace',
                'integration_type' => 'learning',
                'region' => 'apac',
                'environment' => 'production',
                'status' => 'live',
                'latency_ms' => 180,
                'last_synced_at' => $now,
                'throughput_per_minute' => 650,
                'error_rate' => 0.3,
                'uptime_percentage' => 99.99,
                'active_automations' => 31,
                'channels' => json_encode(['Drive', 'Meet', 'Classroom']),
                'metadata' => json_encode(['uptime' => 99.99]),
                'source' => 'seed',
                'display_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        IntegrationEvent::query()->insert([
            [
                'integration_slug' => 'finance-erp',
                'region' => 'emea',
                'integration_type' => 'finance',
                'severity' => 'success',
                'title' => 'Finance ERP Sync Complete',
                'detail' => '312 invoices reconciled • 0 errors',
                'occurred_at' => Carbon::now()->subMinutes(10),
                'meta' => json_encode(['records' => 312]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'integration_slug' => 'messaging-stack',
                'region' => 'emea',
                'integration_type' => 'communication',
                'severity' => 'warning',
                'title' => 'WhatsApp Webhook Retries',
                'detail' => 'Automatic failover to Meta Cloud API',
                'occurred_at' => Carbon::now()->subMinutes(5),
                'meta' => json_encode(['retries' => 12]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'integration_slug' => 'data-warehouse',
                'region' => 'amer',
                'integration_type' => 'analytics',
                'severity' => 'info',
                'title' => 'Data Lake Snapshot',
                'detail' => 'Quarterly compliance export stored in S3',
                'occurred_at' => Carbon::now()->subHours(1),
                'meta' => json_encode(['size_gb' => 18]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
