<?php

namespace App\Http\Controllers\Tenant\Settings;

use App\Http\Controllers\Controller;
use App\Models\TenantPaymentGatewayConfig;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentGatewaysController extends Controller
{
    /**
     * Show payment methods configuration page.
     */
    public function index(): View
    {
        $gateways = TenantPaymentGatewayConfig::ordered()->get();
        
        // Available gateways
        $availableGateways = [
            'paypal' => [
                'name' => 'PayPal',
                'logo' => '🅿️',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text', 'required' => true],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password', 'required' => true],
                    'webhook_id' => ['label' => 'Webhook ID', 'type' => 'text', 'required' => false],
                ],
                'currencies' => ['USD', 'EUR', 'GBP', 'KES', 'UGX', 'NGN', 'ZAR'],
                'description' => 'Accept student fee payments via PayPal',
            ],
            'flutterwave' => [
                'name' => 'Flutterwave',
                'logo' => '💳',
                'fields' => [
                    'public_key' => ['label' => 'Public Key', 'type' => 'text', 'required' => true],
                    'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                    'encryption_key' => ['label' => 'Encryption Key', 'type' => 'password', 'required' => true],
                ],
                'currencies' => ['NGN', 'KES', 'UGX', 'GHS', 'ZAR', 'USD', 'EUR'],
                'description' => 'Accept student payments across Africa',
            ],
            'pesapal' => [
                'name' => 'PesaPal',
                'logo' => '💰',
                'fields' => [
                    'consumer_key' => ['label' => 'Consumer Key', 'type' => 'text', 'required' => true],
                    'consumer_secret' => ['label' => 'Consumer Secret', 'type' => 'password', 'required' => true],
                    'ipn_id' => ['label' => 'IPN ID', 'type' => 'text', 'required' => false],
                ],
                'currencies' => ['KES', 'UGX', 'TZS', 'RWF', 'USD'],
                'description' => 'East Africa payment gateway for school fees',
            ],
            'dpo' => [
                'name' => 'DPO Pay',
                'logo' => '💵',
                'fields' => [
                    'company_token' => ['label' => 'Company Token', 'type' => 'text', 'required' => true],
                    'service_type' => ['label' => 'Service Type', 'type' => 'text', 'required' => true],
                    'company_ref' => ['label' => 'Company Reference', 'type' => 'text', 'required' => false],
                ],
                'currencies' => ['KES', 'UGX', 'TZS', 'ZAR', 'NGN', 'GHS', 'USD', 'EUR'],
                'description' => 'Process school fees across Africa',
            ],
            'mtn_momo' => [
                'name' => 'MTN Mobile Money',
                'logo' => '📱',
                'fields' => [
                    'api_user' => ['label' => 'API User', 'type' => 'text', 'required' => true],
                    'api_key' => ['label' => 'API Key', 'type' => 'password', 'required' => true],
                    'subscription_key' => ['label' => 'Subscription Key', 'type' => 'password', 'required' => true],
                    'currency' => ['label' => 'Currency', 'type' => 'select', 'options' => ['UGX', 'GHS', 'ZMW', 'XAF'], 'required' => true],
                ],
                'currencies' => ['UGX', 'GHS', 'ZMW', 'XAF'],
                'description' => 'MTN Mobile Money for student fees',
            ],
            'airtel_money' => [
                'name' => 'Airtel Money',
                'logo' => '📞',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text', 'required' => true],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password', 'required' => true],
                    'pin' => ['label' => 'PIN', 'type' => 'password', 'required' => true],
                ],
                'currencies' => ['UGX', 'KES', 'TZS', 'RWF', 'ZMW', 'MWK'],
                'description' => 'Airtel Money for school fees',
            ],
            'mpesa' => [
                'name' => 'M-PESA',
                'logo' => '💸',
                'fields' => [
                    'consumer_key' => ['label' => 'Consumer Key', 'type' => 'text', 'required' => true],
                    'consumer_secret' => ['label' => 'Consumer Secret', 'type' => 'password', 'required' => true],
                    'shortcode' => ['label' => 'Shortcode/Paybill', 'type' => 'text', 'required' => true],
                    'passkey' => ['label' => 'Passkey', 'type' => 'password', 'required' => true],
                    'initiator_name' => ['label' => 'Initiator Name', 'type' => 'text', 'required' => false],
                ],
                'currencies' => ['KES'],
                'description' => 'Kenya M-PESA for student fee collection',
            ],
        ];

        return view('tenant.settings.payment-gateways', [
            'gateways' => $gateways,
            'availableGateways' => $availableGateways,
        ]);
    }

    /**
     * Store or update gateway configuration.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'gateway' => 'required|string|in:paypal,flutterwave,pesapal,dpo,mtn_momo,airtel_money,mpesa',
            'is_active' => 'boolean',
            'is_test_mode' => 'boolean',
            'credentials' => 'required|array',
            'supported_currencies' => 'nullable|array',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $config = TenantPaymentGatewayConfig::updateOrCreate(
            ['gateway' => $request->gateway],
            [
                'is_active' => $request->boolean('is_active'),
                'is_test_mode' => $request->boolean('is_test_mode', true),
                'supported_currencies' => $request->supported_currencies,
                'display_order' => $request->display_order ?? 0,
            ]
        );

        $config->setEncryptedCredentials($request->credentials);
        $config->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment gateway configured successfully',
            'gateway' => $config->gateway,
        ]);
    }

    /**
     * Toggle gateway active status.
     */
    public function toggle(Request $request, string $gateway): JsonResponse
    {
        $config = TenantPaymentGatewayConfig::where('gateway', $gateway)->firstOrFail();

        $config->is_active = !$config->is_active;
        $config->save();

        return response()->json([
            'success' => true,
            'is_active' => $config->is_active,
        ]);
    }

    /**
     * Delete gateway configuration.
     */
    public function destroy(string $gateway): JsonResponse
    {
        TenantPaymentGatewayConfig::where('gateway', $gateway)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment gateway deleted successfully',
        ]);
    }
}
