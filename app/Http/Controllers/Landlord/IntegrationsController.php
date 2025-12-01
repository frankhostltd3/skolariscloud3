<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Services\Integrations\IntegrationHealthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class IntegrationsController extends Controller
{
    public function index(Request $request, IntegrationHealthService $integrationHealthService): View
    {
        $region = $request->filled('region') ? $request->input('region') : null;
        $integrationType = $request->filled('type') ? $request->input('type') : null;

        $payload = $integrationHealthService->getDashboardPayload($region, $integrationType);

        return view('landlord.integrations.index', array_merge($payload, [
            'selectedRegion' => $region,
            'selectedType' => $integrationType,
        ]));
    }
}
