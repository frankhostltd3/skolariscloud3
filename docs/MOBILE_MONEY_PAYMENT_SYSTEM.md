# Mobile Money Payment Processing System

## Overview

This documentation covers the comprehensive payment processing system that allows any mobile money system worldwide to be configured and used for processing payments in the SkolariCloud application.

## Table of Contents

1. [Architecture](#architecture)
2. [Gateway Configuration](#gateway-configuration)
3. [Payment Processing Service](#payment-processing-service)
4. [Webhook Handling](#webhook-handling)
5. [User Interface](#user-interface)
6. [API Endpoints](#api-endpoints)
7. [Supported Providers](#supported-providers)
8. [Security Features](#security-features)
9. [Extending for New Providers](#extending-for-new-providers)

---

## Architecture

The payment system consists of several layers:

```
┌─────────────────────────────────────────────────────────────┐
│                     User Interface                          │
│  (Payment Form → Status Page → History)                     │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│              MobileMoneyPaymentController                    │
│  (Create payment → Check status → Cancel)                    │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│              MobileMoneyPaymentService                       │
│  (Payment logic → Provider routing → Status mapping)         │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│              MobileMoneyGateway Model                        │
│  (Configuration → Encryption → Endpoints)                    │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│              External Payment Providers                      │
│  (MTN MoMo, M-Pesa, Airtel, Flutterwave, etc.)              │
└─────────────────────────────────────────────────────────────┘
```

---

## Gateway Configuration

### Database Table: `mobile_money_gateways`

Each gateway configuration includes:

| Field              | Description                               |
| ------------------ | ----------------------------------------- |
| `name`             | Display name (e.g., "MTN Mobile Money")   |
| `slug`             | URL-friendly identifier                   |
| `provider`         | Provider type (e.g., `mtn_momo`, `mpesa`) |
| `api_base_url`     | Base URL for API calls (encrypted)        |
| `public_key`       | Public/API key (encrypted)                |
| `secret_key`       | Secret key (encrypted)                    |
| `api_user`         | API username (encrypted)                  |
| `api_password`     | API password (encrypted)                  |
| `subscription_key` | Subscription key for sandbox (encrypted)  |
| `merchant_id`      | Merchant/Business ID                      |
| `short_code`       | Short code for USSD (M-Pesa)              |
| `callback_url`     | Webhook URL for notifications             |
| `environment`      | `sandbox` or `production`                 |
| `currency_code`    | Default currency (e.g., UGX, KES, USD)    |
| `is_active`        | Gateway enabled/disabled                  |
| `is_default`       | Primary gateway for the school            |

### Admin Configuration

Access: **Settings → Admin → Mobile Money Gateways**

1. Click "Add New Gateway"
2. Select provider (25+ pre-configured templates)
3. Fill in API credentials
4. Set environment (sandbox for testing)
5. Test connection
6. Enable and set as default

---

## Payment Processing Service

### Location: `app/Services/Payments/MobileMoneyPaymentService.php`

### Key Methods

```php
// Initialize with a gateway
$service = new MobileMoneyPaymentService();
$service->setGateway($gateway);

// Or use default
$service->useDefault();

// Or use by slug
$service->useGateway('mtn-uganda');

// Initiate payment
$result = $service->initiatePayment([
    'amount' => 50000,
    'phone' => '0772123456',
    'description' => 'School Fees Term 1',
]);

// Check status
$result = $service->checkStatus($transactionId);

// Handle webhook
$result = $service->handleWebhook($payload);
```

### PaymentResult Object

```php
class PaymentResult {
    public bool $success;
    public string $status;       // pending, completed, failed, etc.
    public ?string $transactionId;
    public ?string $providerTransactionId;
    public ?float $amount;
    public ?string $message;
    public ?string $errorCode;
    public ?string $paymentUrl;  // For redirect-based flows
    public array $providerResponse;
}
```

---

## Webhook Handling

### Location: `app/Http/Controllers/Tenant/Api/PaymentWebhookController.php`

### Webhook URLs

| Provider    | Webhook URL                                |
| ----------- | ------------------------------------------ |
| Generic     | `POST /api/payments/webhooks/{provider}`   |
| MTN MoMo    | `POST /api/payments/webhooks/mtn-momo`     |
| M-Pesa      | `POST /api/payments/webhooks/mpesa`        |
| Airtel      | `POST /api/payments/webhooks/airtel-money` |
| Flutterwave | `POST /api/payments/webhooks/flutterwave`  |
| Paystack    | `POST /api/payments/webhooks/paystack`     |

### Webhook Flow

1. Provider sends callback to webhook URL
2. Controller verifies signature (if applicable)
3. Extracts transaction reference from payload
4. Finds transaction in database
5. Updates transaction status
6. Triggers post-payment actions (update invoice, send notification)

---

## User Interface

### Payment Form

**URL:** `/payments/mobile-money`

Features:
- Gateway selection with visual cards
- Amount input with currency display
- Phone number input with validation
- Optional payment description
- Clear instructions for users

### Payment Status Page

**URL:** `/payments/mobile-money/status/{transactionId}`

Features:
- Real-time status updates (auto-refresh every 5 seconds)
- Visual status indicators (spinner, checkmark, cross)
- Transaction details display
- Action buttons (cancel, retry)
- Help text for troubleshooting

### Payment History

**URL:** `/payments/mobile-money/history`

Features:
- Paginated transaction list
- Status filters
- Date range filters
- Statistics cards
- Export options (planned)

---

## API Endpoints

### Public Endpoints (No Auth)

```
POST /api/payments/webhooks/{provider}    # Receive webhooks
GET  /api/payments/check/{transactionId}  # Check status
```

### Authenticated Endpoints

```
GET  /api/payments/gateways               # List available gateways
POST /api/payments/initiate               # Start payment
GET  /api/payments/status/{id}            # Check status
GET  /api/payments/history                # Transaction history
GET  /api/payments/stats                  # Statistics
POST /api/payments/{id}/cancel            # Cancel payment
POST /api/payments/{id}/retry             # Retry failed payment
```

### API Request Example

```bash
curl -X POST https://school.skolaricloud.com/api/payments/initiate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 50000,
    "phone_number": "0772123456",
    "gateway_id": 1,
    "description": "School Fees",
    "invoice_id": 123
  }'
```

### API Response Example

```json
{
  "success": true,
  "status": "pending",
  "message": "Payment request sent. Please approve on your phone.",
  "transaction_id": "TXN-A1B2C3D4-1699123456",
  "transaction": {
    "id": 1,
    "transaction_id": "TXN-A1B2C3D4-1699123456",
    "amount": "50,000.00 UGX",
    "status": "pending"
  }
}
```

---

## Supported Providers

### Africa

| Provider         | Countries                                       | Type       |
| ---------------- | ----------------------------------------------- | ---------- |
| MTN Mobile Money | Uganda, Ghana, Cameroon, Rwanda, Côte d'Ivoire  | Direct API |
| M-Pesa           | Kenya, Tanzania                                 | Daraja API |
| Airtel Money     | Uganda, Kenya, Tanzania, Rwanda, Ghana, Nigeria | Direct API |
| Orange Money     | Senegal, Mali, Côte d'Ivoire, Guinea            | Direct API |
| Yo! Payments     | Uganda                                          | Aggregator |

### Payment Aggregators

| Provider    | Coverage                                              |
| ----------- | ----------------------------------------------------- |
| Flutterwave | Nigeria, Kenya, Ghana, South Africa, Tanzania, Uganda |
| Paystack    | Nigeria, Ghana, South Africa, Kenya                   |
| DPO Group   | 20+ African countries                                 |

### International

| Provider    | Coverage       |
| ----------- | -------------- |
| Stripe      | Global         |
| PayPal      | Global         |
| GCash       | Philippines    |
| GrabPay     | Southeast Asia |
| Paytm       | India          |
| GoPay       | Indonesia      |
| PIX         | Brazil         |
| MercadoPago | Latin America  |

---

## Security Features

### Credential Encryption

All sensitive fields are encrypted using Laravel's AES-256 encryption:

```php
// Automatic encryption on set
$gateway->public_key = 'pk_live_xxxxx';  // Encrypted before storage

// Automatic decryption on get
echo $gateway->public_key;  // Decrypted on access
```

### Webhook Signature Verification

```php
// Flutterwave
'verif-hash' header === stored webhook_secret

// Paystack
HMAC-SHA512(payload, secret_key) === 'x-paystack-signature' header

// Stripe
Stripe\Webhook::constructEvent() verification
```

### Transaction Security

- Unique transaction IDs generated server-side
- Request IP and User-Agent logged
- All API calls logged for audit
- Failed attempts tracked

---

## Extending for New Providers

### 1. Add to Provider List

In `MobileMoneyGateway.php`:

```php
protected static $providers = [
    // ... existing providers
    'new_provider' => [
        'name' => 'New Provider',
        'country' => 'XX',
        'currency' => 'XXX',
        'endpoints' => [
            'sandbox' => [
                'base' => 'https://sandbox.newprovider.com/api',
                'token' => '/token',
                'request_to_pay' => '/payment',
                'request_status' => '/payment/{referenceId}',
            ],
            'production' => [
                'base' => 'https://api.newprovider.com/api',
                // ... same structure
            ],
        ],
        'required_fields' => ['api_key', 'secret_key', 'merchant_id'],
    ],
];
```

### 2. Add Handler Methods

In `MobileMoneyPaymentService.php`:

```php
protected function initiateNewProvider(array $data): PaymentResult
{
    // Implementation
}

protected function checkNewProviderStatus(string $transactionId): PaymentResult
{
    // Implementation
}

protected function handleNewProviderWebhook(array $payload): PaymentResult
{
    // Implementation
}
```

### 3. Add to Router

In `initiatePayment()` and `checkStatus()`:

```php
return match($this->gateway->provider) {
    // ... existing cases
    'new_provider' => $this->initiateNewProvider($data),
};
```

---

## Database Schema

### PaymentTransaction Table

```sql
CREATE TABLE payment_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    school_id BIGINT NOT NULL,
    gateway_id BIGINT NULL,
    transaction_id VARCHAR(100) UNIQUE NOT NULL,
    external_id VARCHAR(255) NULL,
    request_id VARCHAR(100) NULL,
    amount DECIMAL(15,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'USD',
    phone_number VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    customer_name VARCHAR(100) NULL,
    payable_type VARCHAR(255) NULL,
    payable_id BIGINT NULL,
    description VARCHAR(500) NULL,
    metadata JSON NULL,
    status ENUM('pending','processing','completed','failed','cancelled','expired','refunded'),
    failure_reason TEXT NULL,
    failure_code VARCHAR(50) NULL,
    provider_request JSON NULL,
    provider_response JSON NULL,
    callback_data JSON NULL,
    initiated_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    callback_received_at TIMESTAMP NULL,
    processing_time_ms INT NULL,
    initiated_by BIGINT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

---

## Configuration Checklist

### For Each Gateway:

- [ ] Provider selected
- [ ] API Base URL configured
- [ ] API credentials entered
- [ ] Webhook URL registered with provider
- [ ] Environment set (sandbox/production)
- [ ] Currency configured
- [ ] Connection tested successfully
- [ ] Gateway enabled
- [ ] Set as default (if primary)

### Server Requirements:

- [ ] PHP 8.1+ with cURL extension
- [ ] HTTPS enabled (required for webhooks)
- [ ] Firewall allows outbound API calls
- [ ] Webhook endpoint publicly accessible
- [ ] Cron job for expired transaction cleanup

---

## Troubleshooting

### Payment Not Appearing on Phone

1. Verify phone number format (include country code)
2. Check if user's phone has network
3. Verify gateway is in correct environment
4. Check API credentials are valid
5. View provider response in transaction details

### Webhook Not Received

1. Verify webhook URL is publicly accessible
2. Check SSL certificate is valid
3. Confirm webhook registered with provider
4. Check server logs for incoming requests
5. Verify webhook signature configuration

### Transaction Stuck in Pending

1. Check status manually via provider dashboard
2. Use "Refresh Status" button
3. Check if callback was missed
4. Verify webhook URL is correct
5. Look for errors in application logs

---

## Version History

| Version | Date       | Changes                            |
| ------- | ---------- | ---------------------------------- |
| 1.0.0   | 2024-11-30 | Initial release with 25+ providers |

---

## Support

For technical support or feature requests, contact the development team or create an issue in the project repository.
