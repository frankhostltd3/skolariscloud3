<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\DomainOrder;
use App\Models\School;
use App\Services\SpaceshipRegistrarService;
use App\Services\InternetbsRegistrarService;
use App\Services\DnsManagementService;
use App\Services\SslCertificateService;
use App\Services\ContaboHostingService;
use App\Services\SpaceshipHostingService;
use App\Jobs\VerifyDnsPropagationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DomainOrderController extends Controller {
    protected $spaceshipRegistrar;
    protected $internetbsRegistrar;
    protected $dnsService;
    protected $sslService;
    protected $contaboHosting;
    protected $spaceshipHosting;

    public function __construct(
        SpaceshipRegistrarService $spaceshipRegistrar,
        InternetbsRegistrarService $internetbsRegistrar,
        DnsManagementService $dnsService,
        SslCertificateService $sslService,
        ContaboHostingService $contaboHosting,
        SpaceshipHostingService $spaceshipHosting
    ) {
        $this->spaceshipRegistrar = $spaceshipRegistrar;
        $this->internetbsRegistrar = $internetbsRegistrar;
        $this->dnsService = $dnsService;
        $this->sslService = $sslService;
        $this->contaboHosting = $contaboHosting;
    $this->spaceshipHosting = $spaceshipHosting;
}

/**
 * Display all domain orders
 */
public function index(Request $request)
    {
        $query = DomainOrder::with(['school', 'creator', 'approver']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(20);
        $schools = School::orderBy('name')->get();

        return view('landlord.domains.orders.index', compact('orders', 'schools'));
    }

    /**
     * Show single domain order
     */
    public function show(DomainOrder $order)
    {
        $order->load(['school', 'creator', 'approver']);

        return view('landlord.domains.orders.show', compact('order'));
    }

    /**
     * Store new domain order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_type' => 'required|in:subdomain,custom',
            'school_id' => 'required|exists:schools,id',

            // For custom domains
            'domain_name' => 'required_if:domain_type,custom|string|max:255',
            'tld' => 'required_if:domain_type,custom|string|max:20',
            'billing_cycle' => 'required_if:domain_type,custom|in:1year,2years,3years,5years',

            // For subdomains
            'subdomain' => 'required_if:domain_type,subdomain|string|max:255|unique:schools,subdomain',

            // Contact information
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',

            // Payment
            'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer',
        ]);

        DB::beginTransaction();
        try {
            $school = School::findOrFail($validated['school_id']);

            // Handle subdomain order (free)
            if ($validated['domain_type'] === 'subdomain') {
                $order = $this->createSubdomainOrder($school, $validated);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Subdomain activated successfully!',
                    'order_id' => $order->id,
                    'redirect' => route('landlord.domains.orders.show', $order),
                ]);
            }

            // Handle custom domain order
            $fullDomain = $validated['domain_name'] . $validated['tld'];


            // Choose registrar based on request (spaceship or internetbs)
            $registrar = $request->input('registrar', 'spaceship');
            $registration = null;
            $price = 0;
            $order = null;

            if ($registrar === 'spaceship') {
                $availability = $this->spaceshipRegistrar->checkAvailability($fullDomain);
                if (!$availability['available']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Domain is not available for registration',
                    ], 422);
                }
                $price = $availability['price'] ?? 0;
                $order = DomainOrder::create([
                    'school_id' => $school->id,
                    'domain_name' => $validated['domain_name'],
                    'tld' => $validated['tld'],
                    'contact_name' => $validated['contact_name'],
                    'contact_email' => $validated['contact_email'],
                    'contact_phone' => $validated['contact_phone'],
                    'billing_entity' => 'school',
                    'billing_cycle' => $validated['billing_cycle'],
                    'amount' => $price,
                    'currency' => 'USD',
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'registrar' => 'spaceship',
                    'verification_token' => Str::random(32),
                    'created_by' => auth()->id(),
                ]);
                $registration = $this->spaceshipRegistrar->registerDomain([
                    'domain' => $fullDomain,
                    'years' => $this->getBillingYears($validated['billing_cycle']),
                    'contact_name' => $validated['contact_name'],
                    'contact_email' => $validated['contact_email'],
                    'contact_phone' => $validated['contact_phone'],
                ]);
            } else {
                $availability = $this->internetbsRegistrar->checkAvailability($fullDomain);
                if (!$availability['available']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Domain is not available for registration',
                    ], 422);
                }
                $price = $availability['price'] ?? 0;
                $order = DomainOrder::create([
                    'school_id' => $school->id,
                    'domain_name' => $validated['domain_name'],
                    'tld' => $validated['tld'],
                    'contact_name' => $validated['contact_name'],
                    'contact_email' => $validated['contact_email'],
                    'contact_phone' => $validated['contact_phone'],
                    'billing_entity' => 'school',
                    'billing_cycle' => $validated['billing_cycle'],
                    'amount' => $price,
                    'currency' => 'USD',
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'registrar' => 'internetbs',
                    'verification_token' => Str::random(32),
                    'created_by' => auth()->id(),
                ]);
                $registration = $this->internetbsRegistrar->registerDomain([
                    'domain' => $fullDomain,
                    'years' => $this->getBillingYears($validated['billing_cycle']),
                    'contact_name' => $validated['contact_name'],
                    'contact_email' => $validated['contact_email'],
                    'contact_phone' => $validated['contact_phone'],
                ]);
            }

            if ($registration['success']) {
                $order->update([
                    'registrar_order_id' => $registration['order_id'],
                    'registered_at' => now(),
                    'expires_at' => $registration['expires_at'],
                    'status' => 'reviewing',
                ]);

                // Create Cloudflare zone
                $zone = $this->dnsService->createZone($fullDomain, config('services.cloudflare.account_id'));
                if ($zone['success']) {
                    $records = $this->dnsService->generateTenantRecords($fullDomain, $school->subdomain);
                    $this->dnsService->createRecords($zone['zone_id'], $records);
                    $order->update([
                        'dns_records' => $records,
                        'cloudflare_zone_id' => $zone['zone_id'],
                        'nameservers' => implode(',', $zone['name_servers']),
                    ]);
                    VerifyDnsPropagationJob::dispatch($order->id)->delay(now()->addMinutes(5));
                }

                // Hosting provisioning (optional)
                if ($request->filled('hosting_provider') && $request->filled('hosting_plan_id')) {
                    $hostingProvider = $request->input('hosting_provider');
                    $hostingPlanId = $request->input('hosting_plan_id');
                    $hostingResult = null;
                    if ($hostingProvider === 'contabo') {
                        $hostingResult = $this->contaboHosting->provisionHosting([
                            'domain' => $fullDomain,
                            'plan_id' => $hostingPlanId,
                            'school_id' => $school->id,
                            'contact_name' => $validated['contact_name'],
                            'contact_email' => $validated['contact_email'],
                            'contact_phone' => $validated['contact_phone'],
                        ]);
                    } elseif ($hostingProvider === 'spaceship') {
                        $hostingResult = $this->spaceshipHosting->provisionHosting([
                            'domain' => $fullDomain,
                            'plan_id' => $hostingPlanId,
                            'school_id' => $school->id,
                            'contact_name' => $validated['contact_name'],
                            'contact_email' => $validated['contact_email'],
                            'contact_phone' => $validated['contact_phone'],
                        ]);
                    }
                    if ($hostingResult && $hostingResult['success']) {
                        $order->update([
                            'hosting_provider' => $hostingProvider,
                            'hosting_id' => $hostingResult['hosting_id'],
                            'hosting_details' => $hostingResult['details'],
                        ]);
                    }
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Domain order created successfully! DNS verification in progress.',
                    'order_id' => $order->id,
                    'redirect' => route('landlord.domains.orders.show', $order),
                    'nameservers' => $zone['name_servers'] ?? [],
                ]);
            }

            throw new \Exception($registration['error'] ?? 'Domain registration failed');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Domain order creation failed', [
                'error' => $e->getMessage(),
                'school_id' => $validated['school_id'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Order creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve domain order
     */
    public function approve(DomainOrder $order)
    {
        if ($order->status !== 'reviewing') {
            return back()->with('error', 'Only orders under review can be approved');
        }

        $order->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Domain order approved successfully');
    }

    /**
     * Reject domain order
     */
    public function reject(Request $request, DomainOrder $order)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $order->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Domain order rejected');
    }

    /**
     * Activate domain routing
     */
    public function activateRouting(DomainOrder $order)
    {
        if (!$order->dns_verified) {
            return back()->with('error', 'DNS must be verified before activating routing');
        }

        if (!$order->ssl_enabled || $order->ssl_status !== 'active') {
            return back()->with('error', 'SSL must be active before enabling routing');
        }

        $order->update(['routing_active' => true]);

        return back()->with('success', 'Domain routing activated successfully');
    }

    /**
     * Check domain availability
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'domain' => 'required|string',
        ]);

        $result = $this->registrarService->checkAvailability($request->domain);

        return response()->json($result);
    }

    /**
     * Create subdomain order (free, instant activation)
     */
    protected function createSubdomainOrder(School $school, array $data): DomainOrder
    {
        $order = DomainOrder::create([
            'school_id' => $school->id,
            'domain_name' => $data['subdomain'],
            'tld' => '.skolariscloud.com',
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'],
            'billing_entity' => 'school',
            'billing_cycle' => 'free',
            'amount' => 0,
            'currency' => 'USD',
            'payment_method' => 'none',
            'payment_status' => 'completed',
            'status' => 'active',
            'dns_verified' => true,
            'ssl_enabled' => true,
            'ssl_status' => 'active',
            'routing_active' => true,
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Update school subdomain
        $school->update(['subdomain' => $data['subdomain']]);

        return $order;
    }

    /**
     * Convert billing cycle to years
     */
    protected function getBillingYears(string $cycle): int
    {
        return match ($cycle) {
            '1year' => 1,
            '2years' => 2,
            '3years' => 3,
            '5years' => 5,
            default => 1,
        };
    }
}
