# Skolaris Fees Pay

A Drop-in Laravel package to handle school fees payments for a multi-tenant SaaS.

## Features

- **Multi-tenancy**: Supports database-per-tenant architecture with automatic switching middleware.
- **Payment Gateways**:
  - Flutterwave (Card, Mobile Money)
  - Stripe (Card)
  - PayPal
- **Receipts**:
  - PDF Receipts (using dompdf)
  - Email Receipts
- **Notifications**:
  - SMS (via adapter)
  - WhatsApp
  - Email

## Installation

1. Install via Composer:
   ```bash
   composer require skolaris/fees-pay
   ```

2. Publish configuration:
   ```bash
   php artisan vendor:publish --provider="Skolaris\FeesPay\SkolarisFeesPayServiceProvider"
   ```

3. Run migrations:
   ```bash
   php artisan migrate
   ```

## Configuration

Edit `config/fees-pay.php` to set up your payment gateway keys and notification providers.

## Usage

### Middleware

Add the `SwitchTenantDatabase` middleware to your routes to enable tenant database switching:

```php
Route::middleware([\Skolaris\FeesPay\Http\Middleware\SwitchTenantDatabase::class])->group(function () {
    // Tenant specific routes
});
```

### Payment Routes

The package provides the following routes:
- POST `/fees/pay`: Initiate a payment.
- GET `/fees/callback`: Handle payment callback.

## License

MIT
