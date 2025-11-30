<?php

namespace App\Http\Controllers\Landlord\Tenants;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Contracts\View\View;

class DomainsController extends Controller
{
    public function __invoke(): View
    {
        $tenants = School::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(function (School $school) {
                $meta = $school->meta ?? [];

                return [
                    'id' => $school->id,
                    'name' => $school->name,
                    'admin_email' => $meta['admin_email'] ?? null,
                    'subdomain' => $school->subdomain,
                    'domain' => $school->domain,
                ];
            });

        return view('landlord.tenants.domains', compact('tenants'));
    }
}
