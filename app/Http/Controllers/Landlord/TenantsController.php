<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TenantsExport;

class TenantsController extends Controller
{
    public function index(Request $request): View
    {
        $query = School::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subdomain', 'like', "%{$search}%")
                  ->orWhere('domain', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_EXTRACT(meta, '$.admin_email') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('plan')) {
            $query->whereRaw("JSON_EXTRACT(meta, '$.plan') = ?", [$request->plan]);
        }

        if ($request->filled('country')) {
            $query->whereRaw("JSON_EXTRACT(meta, '$.country') = ?", [$request->country]);
        }

        $tenants = $query->latest()->paginate(12)->withQueryString();

        $tenants->getCollection()->transform(function (School $school) {
            $meta = $school->meta ?? [];

            $school->setAttribute('display_name', $school->name);
            $school->setAttribute('plan_value', $meta['plan'] ?? null);
            $school->setAttribute('country_code', $meta['country'] ?? null);
            $school->setAttribute('admin_email', $meta['admin_email'] ?? null);
            $school->setAttribute('contact_email', $meta['contact_email'] ?? ($meta['admin_email'] ?? null));

            $phones = $meta['phones'] ?? [];
            if (is_string($phones)) {
                $phones = array_filter(array_map('trim', explode(',', $phones)));
            }
            $school->setAttribute('phones', $phones);

            // Construct primary domain
            $primaryDomain = $school->domain;
            if (!$primaryDomain && $school->subdomain) {
                $centralDomain = config('tenancy.central_domain', 'localhost');
                $primaryDomain = $school->subdomain . '.' . $centralDomain;
            }
            $school->setAttribute('primary_domain', $primaryDomain);

            return $school;
        });

        // We don't need a separate domains query anymore as domain is on the school model
        // But the view might expect a collection of domains keyed by tenant_id
        // Let's mock it to avoid breaking the view if it iterates over domains
        $domains = $tenants->mapWithKeys(function ($school) {
            return [$school->id => collect([(object)['domain' => $school->primary_domain]])];
        });

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
        $schools = School::all();

        $sql = "-- Schools Export - " . now()->format('Y-m-d H:i:s') . "\n\n";

        // Export schools table
        $sql .= "-- Schools\n";
        foreach ($schools as $school) {
            $meta = json_encode($school->meta);
            $sql .= "INSERT INTO schools (id, name, code, subdomain, domain, database, meta, created_at, updated_at) VALUES ('{$school->id}', '{$school->name}', '{$school->code}', '{$school->subdomain}', '{$school->domain}', '{$school->database}', '{$meta}', '{$school->created_at}', '{$school->updated_at}');\n";
        }

        $filename = 'schools-export-' . now()->format('Y-m-d') . '.sql';

        return response($sql)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function exportOdata()
    {
        $schools = School::all();

        $tenants = $schools->map(function ($school) {
            return [
                '@odata.id' => route('landlord.tenants.edit', $school),
                'id' => $school->id,
                'school_name' => $school->name ?? '',
                'admin_email' => $school->meta['admin_email'] ?? '',
                'plan' => $school->meta['plan'] ?? 'starter',
                'country' => $school->meta['country'] ?? '',
                'created_at' => $school->created_at->toISOString(),
                'updated_at' => $school->updated_at->toISOString(),
            ];
        });

        $odata = [
            '@odata.context' => url('/landlord/tenants/$metadata'),
            'value' => $tenants,
        ];

        return response()->json($odata);
    }
}
