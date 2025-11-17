<?php

namespace App\Http\Controllers\Landlord\Billing;

use App\Http\Controllers\Controller;
use App\Models\LandlordInvoice;
use App\Models\Tenant;
use App\Services\LandlordBilling\InvoiceBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InvoicesController extends Controller
{
    public function __construct(private readonly InvoiceBuilder $invoiceBuilder)
    {
    }

    public function index(): View
    {
        $tenantOptions = Tenant::query()
            ->select(['id', 'data', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(static function (Tenant $tenant): array {
                $payload = $tenant->getAttribute('data');
                if (is_string($payload)) {
                    $payload = json_decode($payload, true) ?: [];
                }

                $displayName = $payload['name'] ?? $payload['school_name'] ?? Str::headline((string) $tenant->id);
                $plan = $payload['plan'] ?? 'starter';
                $country = $payload['country'] ?? null;

                return [
                    'id' => $tenant->id,
                    'name' => $displayName,
                    'short' => Str::of($displayName)
                        ->explode(' ')
                        ->filter(fn (string $segment): bool => $segment !== '')
                        ->take(2)
                        ->map(fn (string $segment): string => mb_substr($segment, 0, 1))
                        ->implode(''),
                    'plan' => Str::title((string) $plan),
                    'country' => $country ? Str::upper($country) : null,
                    'created_at' => $tenant->created_at,
                ];
            })
            ->all();

        $suggestedLineItems = [
            [
                'label' => __('Annual license'),
                'description' => __('Platform subscription fee'),
                'amount' => 1200,
            ],
            [
                'label' => __('Implementation support'),
                'description' => __('Onboarding, data migration, and training'),
                'amount' => 650,
            ],
            [
                'label' => __('SMS bundle top-up'),
                'description' => __('Communication credits'),
                'amount' => 85,
            ],
            [
                'label' => __('Custom integration'),
                'description' => __('API or SIS bridge work'),
                'amount' => 950,
            ],
        ];

        $centralConnection = config('tenancy.database.central_connection');
        $invoiceTableExists = Schema::connection($centralConnection)->hasTable('landlord_invoices');

        $recentInvoices = [];
        if ($invoiceTableExists) {
            $recentInvoices = LandlordInvoice::query()
                ->latest('issued_at')
                ->latest()
                ->limit(15)
                ->get()
                ->map(static fn (LandlordInvoice $invoice): array => [
                    'number' => $invoice->invoice_number,
                    'tenant' => $invoice->tenant_name_snapshot ?? __('General billing'),
                    'amount' => (float) $invoice->total,
                    'issued' => $invoice->issued_at ?? $invoice->created_at,
                    'status' => Str::title($invoice->status ?? 'draft'),
                ])
                ->all();
        }

        return view('landlord.billing.invoices', [
            'tenantOptions' => $tenantOptions,
            'suggestedLineItems' => $suggestedLineItems,
            'recentInvoices' => $recentInvoices,
            'invoiceTableMissing' => ! $invoiceTableExists,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'tenant_ids' => ['array'],
            'tenant_ids.*' => ['string', 'max:64'],
            'period.start' => ['nullable', 'date'],
            'period.end' => ['nullable', 'date', 'after_or_equal:period.start'],
            'period.due' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.category' => ['nullable', 'string', 'max:64'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $tenantIds = Arr::get($payload, 'tenant_ids', []);
        if (empty($tenantIds)) {
            $tenantIds = [null];
        }

        $created = [];
        foreach ($tenantIds as $tenantId) {
            $invoicePayload = [
                'tenant_id' => $tenantId,
                'status' => 'pending',
                'issued_at' => Arr::get($payload, 'period.start'),
                'due_at' => Arr::get($payload, 'period.due'),
                'period_start' => Arr::get($payload, 'period.start'),
                'period_end' => Arr::get($payload, 'period.end'),
                'items' => Arr::get($payload, 'items', []),
                'notes' => Arr::get($payload, 'notes'),
                'metadata' => ['source' => 'wizard'],
            ];

            $invoice = $this->invoiceBuilder->createFromPayload($invoicePayload);
            $created[] = $invoice->invoice_number;
        }

        return response()->json([
            'created' => $created,
            'count' => count($created),
        ], 201);
    }

    /**
     * Show a specific invoice with payment options
     */
    public function show(LandlordInvoice $invoice): View
    {
        $invoice->load(['items', 'tenant']);
        
        // Get active payment gateways
        $activeGateways = \App\Models\PaymentGatewayConfig::where('context', 'landlord_billing')
            ->where('is_active', true)
            ->get()
            ->map(function ($config) {
                return [
                    'gateway' => $config->gateway,
                    'name' => $config->display_name,
                    'logo' => $config->logo,
                    'requires_email' => in_array($config->gateway, ['paypal', 'flutterwave']),
                    'requires_phone' => in_array($config->gateway, ['mpesa', 'mtn_momo', 'airtel_money']),
                ];
            });
        
        // Get payment transactions for this invoice
        $transactions = \App\Models\PaymentTransaction::where('transaction_type', 'invoice')
            ->where('related_id', $invoice->id)
            ->orderByDesc('created_at')
            ->get();
        
        return view('landlord.billing.invoice-show', compact('invoice', 'activeGateways', 'transactions'));
    }
}
