<?php

namespace App\Http\Controllers\Landlord\Tenants;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Stancl\Tenancy\Database\Models\Tenant;

class DomainsController extends Controller
{
    public function __invoke(): View
    {
        $tenants = Tenant::query()
            ->select(['id', 'data'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Tenant $tenant) {
                $data = $tenant->getAttribute('data');
                if (is_string($data)) {
                    $data = json_decode($data, true) ?: [];
                }

                return [
                    'id' => $tenant->getKey(),
                    'name' => $data['school_name'] ?? $tenant->getKey(),
                    'admin_email' => $data['admin_email'] ?? null,
                ];
            });

        return view('landlord.tenants.domains', compact('tenants'));
    }
}
