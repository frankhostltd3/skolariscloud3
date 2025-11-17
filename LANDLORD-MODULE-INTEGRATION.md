# Landlord Module Integration Guide

## Files Successfully Copied

### 1. Console Commands (2 files)
- CreateLandlordUser.php
- ProcessLandlordInvoices.php

### 2. Controllers (22 files)
- AnalyticsController.php
- AuditLogsController.php
- BillingController.php
- DashboardController.php
- IntegrationsController.php
- NotificationsController.php
- ProfileController.php
- RbacController.php
- SettingsController.php
- SystemHealthController.php
- TenantsController.php
- UsersController.php
- Auth/AuthenticatedSessionController.php
- Billing/DunningController.php
- Billing/InvoicePaymentController.php
- Billing/InvoicesController.php
- Billing/PaymentMethodsController.php
- Billing/PlansController.php
- Tenants/CreateController.php
- Tenants/DomainsController.php
- Tenants/ImportController.php
- Webhooks/PaymentWebhookController.php

### 3. Requests (2 files)
- BillingPlanRequest.php
- UpdateProfileRequest.php

### 4. Models (5 files)
- LandlordAuditLog.php
- LandlordDunningPolicy.php
- LandlordInvoice.php
- LandlordInvoiceItem.php
- LandlordNotification.php

### 5. Notifications (4 files)
- GenericLandlordMessage.php
- LandlordInvoiceSuspended.php
- LandlordInvoiceTerminated.php
- LandlordInvoiceWarning.php

### 6. Services (1 file)
- LandlordBilling/InvoiceBuilder.php

### 7. Factories (2 files)
- LandlordInvoiceFactory.php
- LandlordInvoiceItemFactory.php

### 8. Migrations (5 files)
- 2025_10_01_000001_create_landlord_dunning_policies_table.php
- 2025_10_01_000001_create_landlord_invoices_table.php
- 2025_10_01_000002_create_landlord_invoice_items_table.php
- 2025_10_01_120000_create_landlord_audit_logs_table.php
- 2025_10_01_121000_create_landlord_notifications_table.php

### 9. Views (30+ blade files)
- landlord/dashboard.blade.php
- landlord/analytics/index.blade.php
- landlord/audit/index.blade.php
- landlord/auth/login.blade.php
- landlord/billing/* (multiple files)
- landlord/layouts/* (app.blade.php, guest.blade.php)
- landlord/notifications/*
- landlord/profile/*
- landlord/rbac/*
- landlord/settings/*
- landlord/tenants/*
- landlord/users/*

### 10. Mail Views
- mail/landlord/dunning.blade.php

### 11. Tests
- Feature/Landlord/*
- Feature/LandlordDunningTest.php

### 12. Documentation
- docs/create-landlord-user.md

## Next Steps - IMPORTANT

### Step 1: Add Landlord Routes
Copy the content from `landlord-routes-to-add.php` and add it to `routes/web.php`:
1. Add all the use statements at the top of web.php
2. Add the route group section before the closing of the file

### Step 2: Configure Auth Guard
Add landlord guard to `config/auth.php`:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'landlord' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],
```

And add password reset for landlords:

```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
        'expire' => 60,
        'throttle' => 60,
    ],
    'landlords' => [
        'provider' => 'users',
        'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

### Step 3: Run Migrations
```bash
php artisan migrate
```

This will create:
- landlord_invoices
- landlord_invoice_items
- landlord_dunning_policies
- landlord_audit_logs
- landlord_notifications

### Step 4: Create Landlord User
```bash
php artisan make:landlord-user
```

Or use the command from CreateLandlordUser.php console command.

### Step 5: Seed Permissions
Ensure landlord permissions are seeded. Check if there's a landlord permission seeder or add these permissions:
- access landlord dashboard
- manage tenants
- view billing
- manage invoices
- manage payment methods
- view analytics
- manage settings
- view audit logs
- manage notifications
- manage users
- view system health
- manage integrations

### Step 6: Configure Payment Gateways (Optional)
If using Stripe, PayPal, or other payment gateways:
1. Add credentials to `.env`
2. Configure in landlord settings

### Step 7: Test Landlord Access
1. Visit: `http://localhost/landlord/login`
2. Login with landlord credentials
3. Test dashboard access
4. Verify tenants list loads
5. Test billing section

## Route Summary

### Landlord Routes
- `landlord.login.show` - GET /landlord/login
- `landlord.login.store` - POST /landlord/login
- `landlord.logout` - POST /landlord/logout
- `landlord.dashboard` - GET /landlord/dashboard
- `landlord.tenants.*` - Tenant management routes
- `landlord.billing.*` - Billing and invoicing routes
- `landlord.analytics` - Analytics dashboard
- `landlord.settings` - Settings management
- `landlord.profile` - Profile management
- `landlord.users` - User management
- `landlord.audit` - Audit logs
- `landlord.notifications.*` - Notification management
- `landlord.integrations` - Third-party integrations
- `landlord.health` - System health monitoring
- `landlord.webhooks.handle` - Payment gateway webhooks

## Key Features

1. **Tenant Management**: Create, edit, delete, import tenants
2. **Billing System**: Invoicing, payment methods, dunning policies
3. **Analytics**: Dashboard with key metrics
4. **Audit Logs**: Track all system activities
5. **Notifications**: Send system-wide notifications
6. **RBAC**: Role-based access control
7. **System Health**: Monitor system performance
8. **Integrations**: Third-party service integrations

## Notes

- All landlord routes are prefixed with `/landlord`
- Landlord authentication uses separate guard: `auth:landlord`
- Permissions are checked with: `permission:access landlord dashboard,landlord`
- Webhooks don't require authentication for payment gateway callbacks
- Views use landlord layouts: `landlord.layouts.app` and `landlord.layouts.guest`

## Troubleshooting

If you encounter errors:
1. Clear caches: `php artisan optimize:clear`
2. Check user table has landlord users with proper roles
3. Verify permissions are seeded
4. Check database connection for landlord tables
5. Ensure auth guard is configured properly
