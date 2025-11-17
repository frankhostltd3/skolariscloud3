<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SettingsController extends Controller
{
    public function __invoke(): View
    {
        $notificationOptions = collect([
            ['key' => 'tenant_health', 'label' => __('Tenant health alerts')],
            ['key' => 'billing_events', 'label' => __('Billing events')],
            ['key' => 'domain_status', 'label' => __('Domain DNS status')],
        ]);

        $integrationOptions = collect([
            ['name' => 'Slack', 'status' => 'connected'],
            ['name' => 'HubSpot', 'status' => 'not_connected'],
            ['name' => 'QuickBooks', 'status' => 'beta'],
        ]);

        return view('landlord.settings.index', [
            'notificationOptions' => $notificationOptions,
            'integrationOptions' => $integrationOptions,
        ]);
    }
}
