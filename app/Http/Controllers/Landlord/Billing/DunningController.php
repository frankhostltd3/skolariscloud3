<?php

namespace App\Http\Controllers\Landlord\Billing;

use App\Http\Controllers\Controller;
use App\Models\LandlordDunningPolicy;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DunningController extends Controller
{
    public function index(): View
    {
        $policy = LandlordDunningPolicy::current();

        // Normalize template placeholders back to {{ }} if user-submitted values contain HTML entities
        $templates = $validated['templates'] ?? ($policy->templates ?? []);
        if (is_array($templates)) {
            $decode = static function (string $v): string {
                return str_replace(['&#123;&#123;', '&#125;&#125;'], ['{{', '}}'], $v);
            };
            foreach ($templates as $k => $v) {
                if (is_string($v)) {
                    $templates[$k] = $decode($v);
                }
            }
        }

        return view('landlord.billing.dunning', compact('policy'));
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'warning_threshold_days' => ['required', 'integer', 'min:0'],
            'suspension_grace_days' => ['required', 'integer', 'min:0'],
            'termination_grace_days' => ['required', 'integer', 'min:0'],
            'reminder_windows' => ['nullable', 'string'],
            'late_fee_percent' => ['nullable', 'numeric', 'min:0'],
            'late_fee_flat' => ['nullable', 'numeric', 'min:0'],
            'warning_channels' => ['nullable', 'array'],
            'warning_channels.*' => ['in:mail,sms,slack,webhook'],
            'warning_recipients' => ['nullable', 'string'],
            'suspension_recipients' => ['nullable', 'string'],
            'termination_recipients' => ['nullable', 'string'],
            'warning_phones' => ['nullable', 'string'],
            'suspension_phones' => ['nullable', 'string'],
            'termination_phones' => ['nullable', 'string'],
            'templates' => ['nullable', 'array'],
        ]);

        // Transform inputs
        $reminderWindows = [];
        if (! empty($validated['reminder_windows'])) {
            $reminderWindows = collect(explode(',', (string) $validated['reminder_windows']))
                ->map(fn ($v) => (int) trim((string) $v))
                ->filter(fn ($v) => is_int($v) || $v === 0)
                ->values()
                ->all();
        }

        $emailSplitter = static function (?string $csv): array {
            return collect(explode(',', (string) $csv))
                ->map(fn ($v) => trim((string) $v))
                ->filter()
                ->unique()
                ->values()
                ->all();
        };

    $policy = LandlordDunningPolicy::current();
        $policy->fill([
            'name' => $validated['name'],
            'warning_threshold_days' => (int) $validated['warning_threshold_days'],
            'suspension_grace_days' => (int) $validated['suspension_grace_days'],
            'termination_grace_days' => (int) $validated['termination_grace_days'],
            'reminder_windows' => $reminderWindows,
            'late_fee_percent' => $validated['late_fee_percent'] ?? null,
            'late_fee_flat' => $validated['late_fee_flat'] ?? null,
            'warning_channels' => $validated['warning_channels'] ?? ['mail'],
            // Keep existing suspension/termination channels for now
            'warning_recipients' => $emailSplitter($validated['warning_recipients'] ?? ''),
            'suspension_recipients' => $emailSplitter($validated['suspension_recipients'] ?? ''),
            'termination_recipients' => $emailSplitter($validated['termination_recipients'] ?? ''),
            'warning_phones' => $emailSplitter($validated['warning_phones'] ?? ''),
            'suspension_phones' => $emailSplitter($validated['suspension_phones'] ?? ''),
            'termination_phones' => $emailSplitter($validated['termination_phones'] ?? ''),
            'templates' => $templates,
            'is_active' => true,
        ]);
        $policy->save();

        return redirect()->route('landlord.billing.dunning')
            ->with('success', __('Dunning policy saved successfully.'));
    }

    public function preview(Request $request): View
    {
        $action = $request->query('action', 'warning');
        if (! in_array($action, ['warning', 'suspension', 'termination'], true)) {
            $action = 'warning';
        }
        $policy = LandlordDunningPolicy::current();
        // Minimal sample invoice object-like array for preview
        $invoice = (object) [
            'invoice_number' => 'LLI-PREVIEW-000001',
            'tenant_name_snapshot' => 'Preview Tenant',
            'tenant_id' => 'preview-tenant',
            'total' => 199.99,
            'due_at' => now()->addDays(3),
            'getKey' => fn () => 0,
        ];

        // Render the markdown mail view directly in a simple wrapper
        return view('mail.landlord.dunning', [
            'invoice' => $invoice,
            'policy' => $policy,
            'action' => $action,
        ]);
    }
}
