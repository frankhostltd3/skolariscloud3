<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TenantsExport;

class TenantsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tenant::query()
            ->select(['id', 'data', 'created_at']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_EXTRACT(data, '$.school_name') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(data, '$.admin_email') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('plan')) {
            $query->whereRaw("JSON_EXTRACT(data, '$.plan') = ?", [$request->plan]);
        }

        if ($request->filled('country')) {
            $query->whereRaw("JSON_EXTRACT(data, '$.country') = ?", [$request->country]);
        }

        $tenants = $query->latest()->paginate(12)->withQueryString();

        $tenants->getCollection()->transform(function (Tenant $tenant) {
            $payload = $tenant->getAttribute('data');

            if (is_string($payload)) {
                $payload = json_decode($payload, true) ?: [];
            }

            $tenant->setAttribute('display_name', $payload['school_name'] ?? $tenant->id);
            $tenant->setAttribute('plan_value', $payload['plan'] ?? null);
            $tenant->setAttribute('country_code', $payload['country'] ?? null);
            $tenant->setAttribute('admin_email', $payload['admin_email'] ?? null);
            $tenant->setAttribute('contact_email', $payload['contact_email'] ?? ($payload['admin_email'] ?? null));
            $phones = $payload['phones'] ?? [];
            if (is_string($phones)) { $phones = array_filter(array_map('trim', explode(',', $phones))); }
            $tenant->setAttribute('phones', $phones);

            return $tenant;
        });

        $domains = Domain::query()
            ->whereIn('tenant_id', $tenants->pluck('id'))
            ->get()
            ->groupBy('tenant_id');

        return view('landlord.tenants.index', [
            'tenants' => $tenants,
            'domains' => $domains,
            'filters' => $request->only(['search', 'plan', 'country']),
        ]);
    }

    public function exportExcel()
    {
        return Excel::download(new TenantsExport, 'tenants-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportSql()
    {
        $tenants = Tenant::all();
        $domains = Domain::all();

        $sql = "-- Tenants Export - " . now()->format('Y-m-d H:i:s') . "\n\n";

        // Export tenants table
        $sql .= "-- Tenants\n";
        foreach ($tenants as $tenant) {
            $data = json_encode($tenant->data);
            $sql .= "INSERT INTO tenants (id, data, created_at, updated_at) VALUES ('{$tenant->id}', '{$data}', '{$tenant->created_at}', '{$tenant->updated_at}');\n";
        }

        $sql .= "\n-- Domains\n";
        foreach ($domains as $domain) {
            $sql .= "INSERT INTO domains (id, domain, tenant_id, created_at, updated_at) VALUES ({$domain->id}, '{$domain->domain}', '{$domain->tenant_id}', '{$domain->created_at}', '{$domain->updated_at}');\n";
        }

        $filename = 'tenants-export-' . now()->format('Y-m-d') . '.sql';

        return response($sql)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function exportOdata()
    {
        $tenants = Tenant::all()->map(function ($tenant) {
            $data = $tenant->data;
            return [
                '@odata.id' => route('landlord.tenants.edit', $tenant),
                'id' => $tenant->id,
                'school_name' => $data['school_name'] ?? '',
                'admin_email' => $data['admin_email'] ?? '',
                'plan' => $data['plan'] ?? 'starter',
                'country' => $data['country'] ?? '',
                'created_at' => $tenant->created_at->toISOString(),
                'updated_at' => $tenant->updated_at->toISOString(),
            ];
        });

        $odata = [
            '@odata.context' => url('/landlord/tenants/$metadata'),
            'value' => $tenants,
        ];

        return response()->json($odata);
    }
}
