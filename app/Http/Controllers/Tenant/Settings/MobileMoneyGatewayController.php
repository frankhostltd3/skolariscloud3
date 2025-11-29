<?php

namespace App\Http\Controllers\Tenant\Settings;

use App\Http\Controllers\Controller;
use App\Models\MobileMoneyGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MobileMoneyGatewayController extends Controller
{
    /**
     * Display a listing of mobile money gateways
     */
    public function index()
    {
        $school = request()->attributes->get('currentSchool');
        
        $gateways = MobileMoneyGateway::where('school_id', $school->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        $providerList = MobileMoneyGateway::getProviderList();
        
        return view('tenant.settings.mobile-money.index', compact('gateways', 'providerList'));
    }

    /**
     * Show form for creating a new gateway
     */
    public function create(Request $request)
    {
        $providerList = MobileMoneyGateway::getProviderList();
        $selectedProvider = $request->get('provider', 'custom');
        $providerConfig = $providerList[$selectedProvider] ?? $providerList['custom'];
        
        return view('tenant.settings.mobile-money.create', compact('providerList', 'selectedProvider', 'providerConfig'));
    }

    /**
     * Store a newly created gateway
     */
    public function store(Request $request)
    {
        $school = request()->attributes->get('currentSchool');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|string',
            'country_code' => 'nullable|string|size:2',
            'currency_code' => 'nullable|string|size:3',
            'environment' => 'required|in:sandbox,production',
            
            // API Configuration
            'api_base_url' => 'nullable|url',
            'public_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'api_user' => 'nullable|string',
            'api_password' => 'nullable|string',
            'subscription_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'encryption_key' => 'nullable|string',
            
            // OAuth
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            
            // Merchant Details
            'merchant_id' => 'nullable|string',
            'merchant_name' => 'nullable|string',
            'short_code' => 'nullable|string',
            'till_number' => 'nullable|string',
            'account_number' => 'nullable|string',
            
            // URLs
            'callback_url' => 'nullable|url',
            'return_url' => 'nullable|url',
            'cancel_url' => 'nullable|url',
            
            // Additional
            'supported_networks' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);
        
        // Generate unique slug
        $slug = Str::slug($validated['name'] . '-' . $validated['provider']);
        $count = 1;
        $originalSlug = $slug;
        while (MobileMoneyGateway::where('school_id', $school->id)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        
        $validated['school_id'] = $school->id;
        $validated['slug'] = $slug;
        
        $gateway = MobileMoneyGateway::create($validated);
        
        // If set as default, update others
        if ($request->boolean('is_default')) {
            $gateway->setAsDefault();
        }
        
        return redirect()
            ->route('tenant.settings.mobile-money.index')
            ->with('success', "Mobile money gateway '{$gateway->name}' created successfully!");
    }

    /**
     * Display the specified gateway
     */
    public function show(MobileMoneyGateway $mobileMoneyGateway)
    {
        $this->authorize('view', $mobileMoneyGateway);
        
        return view('tenant.settings.mobile-money.show', [
            'gateway' => $mobileMoneyGateway,
            'providerConfig' => MobileMoneyGateway::getProviderList()[$mobileMoneyGateway->provider] ?? null,
        ]);
    }

    /**
     * Show form for editing the gateway
     */
    public function edit(MobileMoneyGateway $mobileMoneyGateway)
    {
        $this->authorize('update', $mobileMoneyGateway);
        
        $providerList = MobileMoneyGateway::getProviderList();
        $providerConfig = $providerList[$mobileMoneyGateway->provider] ?? $providerList['custom'];
        
        return view('tenant.settings.mobile-money.edit', [
            'gateway' => $mobileMoneyGateway,
            'providerList' => $providerList,
            'providerConfig' => $providerConfig,
        ]);
    }

    /**
     * Update the specified gateway
     */
    public function update(Request $request, MobileMoneyGateway $mobileMoneyGateway)
    {
        $this->authorize('update', $mobileMoneyGateway);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_code' => 'nullable|string|size:2',
            'currency_code' => 'nullable|string|size:3',
            'environment' => 'required|in:sandbox,production',
            
            // API Configuration
            'api_base_url' => 'nullable|url',
            'public_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'api_user' => 'nullable|string',
            'api_password' => 'nullable|string',
            'subscription_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'encryption_key' => 'nullable|string',
            
            // OAuth
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            
            // Merchant Details
            'merchant_id' => 'nullable|string',
            'merchant_name' => 'nullable|string',
            'short_code' => 'nullable|string',
            'till_number' => 'nullable|string',
            'account_number' => 'nullable|string',
            
            // URLs
            'callback_url' => 'nullable|url',
            'return_url' => 'nullable|url',
            'cancel_url' => 'nullable|url',
            
            // Additional
            'supported_networks' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);
        
        // Don't update credentials if empty (preserve existing)
        $credentialFields = ['public_key', 'secret_key', 'api_user', 'api_password', 'subscription_key', 
                            'webhook_secret', 'encryption_key', 'client_id', 'client_secret'];
        
        foreach ($credentialFields as $field) {
            if (empty($validated[$field])) {
                unset($validated[$field]);
            }
        }
        
        $mobileMoneyGateway->update($validated);
        
        // If set as default, update others
        if ($request->boolean('is_default')) {
            $mobileMoneyGateway->setAsDefault();
        }
        
        return redirect()
            ->route('tenant.settings.mobile-money.index')
            ->with('success', "Mobile money gateway '{$mobileMoneyGateway->name}' updated successfully!");
    }

    /**
     * Remove the specified gateway
     */
    public function destroy(MobileMoneyGateway $mobileMoneyGateway)
    {
        $this->authorize('delete', $mobileMoneyGateway);
        
        $name = $mobileMoneyGateway->name;
        $mobileMoneyGateway->delete();
        
        return redirect()
            ->route('tenant.settings.mobile-money.index')
            ->with('success', "Mobile money gateway '{$name}' deleted successfully!");
    }

    /**
     * Toggle gateway active status
     */
    public function toggleActive(MobileMoneyGateway $mobileMoneyGateway)
    {
        $this->authorize('update', $mobileMoneyGateway);
        
        $mobileMoneyGateway->update(['is_active' => !$mobileMoneyGateway->is_active]);
        
        $status = $mobileMoneyGateway->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Gateway '{$mobileMoneyGateway->name}' has been {$status}.");
    }

    /**
     * Set gateway as default
     */
    public function setDefault(MobileMoneyGateway $mobileMoneyGateway)
    {
        $this->authorize('update', $mobileMoneyGateway);
        
        $mobileMoneyGateway->setAsDefault();
        
        return back()->with('success', "'{$mobileMoneyGateway->name}' is now the default payment gateway.");
    }

    /**
     * Test gateway connection
     */
    public function test(MobileMoneyGateway $mobileMoneyGateway)
    {
        $this->authorize('update', $mobileMoneyGateway);
        
        try {
            $result = $this->performConnectionTest($mobileMoneyGateway);
            
            $mobileMoneyGateway->recordTestResult(
                $result['success'],
                $result['message']
            );
            
            if ($result['success']) {
                return back()->with('success', "Connection test passed: {$result['message']}");
            } else {
                return back()->with('error', "Connection test failed: {$result['message']}");
            }
        } catch (\Exception $e) {
            $mobileMoneyGateway->recordTestResult(false, $e->getMessage());
            return back()->with('error', "Connection test error: {$e->getMessage()}");
        }
    }

    /**
     * Perform connection test based on provider
     */
    protected function performConnectionTest(MobileMoneyGateway $gateway): array
    {
        switch ($gateway->provider) {
            case 'mtn_momo':
                return $this->testMtnMomo($gateway);
            case 'mpesa':
                return $this->testMpesa($gateway);
            case 'flutterwave':
                return $this->testFlutterwave($gateway);
            case 'paystack':
                return $this->testPaystack($gateway);
            case 'airtel_money':
                return $this->testAirtelMoney($gateway);
            default:
                return $this->testGenericApi($gateway);
        }
    }

    /**
     * Test MTN MoMo connection
     */
    protected function testMtnMomo(MobileMoneyGateway $gateway): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($gateway->api_user . ':' . $gateway->api_password),
            'Ocp-Apim-Subscription-Key' => $gateway->subscription_key,
        ])->post($gateway->getEndpoint('token'));

        if ($response->successful()) {
            return ['success' => true, 'message' => 'MTN MoMo API connected successfully. Token received.'];
        }
        
        return ['success' => false, 'message' => 'Failed to get access token: ' . $response->body()];
    }

    /**
     * Test M-Pesa connection
     */
    protected function testMpesa(MobileMoneyGateway $gateway): array
    {
        $credentials = base64_encode($gateway->client_id . ':' . $gateway->client_secret);
        
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
        ])->get($gateway->getEndpoint('token'));

        if ($response->successful() && isset($response['access_token'])) {
            return ['success' => true, 'message' => 'M-Pesa API connected successfully. Token received.'];
        }
        
        return ['success' => false, 'message' => 'Failed to authenticate: ' . $response->body()];
    }

    /**
     * Test Flutterwave connection
     */
    protected function testFlutterwave(MobileMoneyGateway $gateway): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $gateway->secret_key,
        ])->get('https://api.flutterwave.com/v3/banks/NG');

        if ($response->successful()) {
            return ['success' => true, 'message' => 'Flutterwave API connected successfully.'];
        }
        
        return ['success' => false, 'message' => 'API error: ' . $response->body()];
    }

    /**
     * Test Paystack connection
     */
    protected function testPaystack(MobileMoneyGateway $gateway): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $gateway->secret_key,
        ])->get('https://api.paystack.co/bank');

        if ($response->successful()) {
            return ['success' => true, 'message' => 'Paystack API connected successfully.'];
        }
        
        return ['success' => false, 'message' => 'API error: ' . $response->body()];
    }

    /**
     * Test Airtel Money connection
     */
    protected function testAirtelMoney(MobileMoneyGateway $gateway): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($gateway->getEndpoint('token'), [
            'client_id' => $gateway->client_id,
            'client_secret' => $gateway->client_secret,
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful() && isset($response['access_token'])) {
            return ['success' => true, 'message' => 'Airtel Money API connected successfully.'];
        }
        
        return ['success' => false, 'message' => 'Failed to authenticate: ' . $response->body()];
    }

    /**
     * Test generic API endpoint
     */
    protected function testGenericApi(MobileMoneyGateway $gateway): array
    {
        if (!$gateway->api_base_url) {
            return ['success' => false, 'message' => 'No API base URL configured.'];
        }
        
        try {
            $response = Http::timeout(10)->get($gateway->api_base_url);
            return ['success' => true, 'message' => "API endpoint reachable. Status: {$response->status()}"];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => "Cannot reach API: {$e->getMessage()}"];
        }
    }

    /**
     * Get providers for a specific country (AJAX)
     */
    public function getProvidersForCountry(Request $request)
    {
        $countryCode = strtoupper($request->get('country', ''));
        
        if (strlen($countryCode) !== 2) {
            return response()->json(['error' => 'Invalid country code'], 400);
        }
        
        $providers = MobileMoneyGateway::getProvidersForCountry($countryCode);
        
        return response()->json($providers);
    }

    /**
     * Get provider configuration (AJAX)
     */
    public function getProviderConfig(Request $request)
    {
        $provider = $request->get('provider', 'custom');
        $providerList = MobileMoneyGateway::getProviderList();
        
        return response()->json($providerList[$provider] ?? $providerList['custom']);
    }
}
