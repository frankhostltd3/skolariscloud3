# Currency System Implementation - Complete

## Overview
Complete multi-currency support system for payment processing in the multi-tenant school management platform. Enables schools to manage multiple currencies with automatic exchange rate conversions.

## Database Schema

### currencies Table (Tenant Connection)
```sql
id              bigint unsigned AUTO_INCREMENT PRIMARY KEY
code            varchar(3) UNIQUE NOT NULL (e.g., USD, EUR, GBP)
name            varchar(255) NOT NULL (e.g., US Dollar, Euro)
symbol          varchar(10) NOT NULL (e.g., $, €, £)
exchange_rate   decimal(15,6) DEFAULT 1.000000
is_default      boolean DEFAULT false
is_active       boolean DEFAULT true
created_at      timestamp
updated_at      timestamp
```

**Migration:** `database/migrations/tenants/2025_11_15_165419_create_currencies_table.php`

## Currency Model

**File:** `app/Models/Currency.php`

### Key Methods

#### `getDefault(): Currency`
Returns the default currency for the tenant. Falls back to USD (code='USD', rate=1.0) if no default is set.

#### `setAsDefault(): void`
Sets this currency as the default. Automatically removes the default flag from all other currencies.

#### `format(float $amount): string`
Formats an amount with the currency symbol.
```php
$currency->format(1234.56); // "$1,234.56"
```

#### `convertTo(float $amount, Currency $toCurrency): float`
Converts amount from this currency to another currency using exchange rates.
```php
$usd = Currency::where('code', 'USD')->first();
$ugx = Currency::where('code', 'UGX')->first();
$ugxAmount = $usd->convertTo(100, $ugx); // 370000.00 UGX
```

#### `scopeActive($query)`
Query scope to filter only active currencies.
```php
Currency::active()->get(); // Returns only active currencies
```

## Controller

**File:** `app/Http/Controllers/Settings/CurrencyController.php`

### Routes
```php
// Resource routes
GET    /settings/currencies              // index()   - List all currencies
GET    /settings/currencies/create       // create()  - Show create form
POST   /settings/currencies              // store()   - Create new currency
GET    /settings/currencies/{id}         // show()    - Not implemented
GET    /settings/currencies/{id}/edit    // edit()    - Show edit form
PUT    /settings/currencies/{id}         // update()  - Update currency
DELETE /settings/currencies/{id}         // destroy() - Delete currency

// Custom routes
POST   /settings/currencies/{id}/set-default    // setDefault()   - Set as default
POST   /settings/currencies/{id}/toggle-active  // toggleActive() - Toggle active status
```

### Validation Rules
```php
'code' => 'required|string|size:3|unique:currencies,code'
'name' => 'required|string|max:255'
'symbol' => 'required|string|max:10'
'exchange_rate' => 'required|numeric|min:0.000001|max:999999999'
'is_active' => 'boolean'
```

### Business Rules
1. **Default Currency Protection:**
   - Cannot delete the default currency
   - Cannot deactivate the default currency
   - Setting a new default automatically removes the flag from others

2. **Exchange Rates:**
   - All exchange rates are relative to USD (1.0)
   - Example: 1 USD = 3700 UGX, so UGX exchange_rate = 3700.000000
   - Decimal precision: 6 decimal places (15,6)

3. **Active Status:**
   - Only active currencies can be used for payments
   - Default currency is always active

## Global Helper Functions

**File:** `app/helpers.php`

### `currentCurrency(): Currency`
Returns the default currency for the current tenant.
```php
$currency = currentCurrency(); // Currency model instance
echo $currency->code; // "USD"
```

### `formatMoney(float $amount, ?Currency $currency = null): string`
Formats an amount with the currency symbol. Uses currentCurrency() if not provided.
```php
formatMoney(1234.56); // "$1,234.56" (default currency)
formatMoney(1234.56, $currency); // "UGX 1,234.56" (specific currency)
```

### `convertCurrency(float $amount, string $fromCode, string $toCode): ?float`
Converts an amount between two currencies using their exchange rates.
```php
convertCurrency(100, 'USD', 'UGX'); // 370000.00
convertCurrency(3700, 'UGX', 'USD'); // 1.00
```

## Currency Seeder

**File:** `database/seeders/CurrencySeeder.php`

### Seeded Currencies (20 Major World Currencies)

| Code | Name                | Symbol | Exchange Rate | Region        |
|------|---------------------|--------|---------------|---------------|
| USD  | US Dollar           | $      | 1.0           | North America |
| EUR  | Euro                | €      | 0.85          | Europe        |
| GBP  | British Pound       | £      | 0.73          | Europe        |
| JPY  | Japanese Yen        | ¥      | 110.0         | Asia          |
| CHF  | Swiss Franc         | CHF    | 0.92          | Europe        |
| CAD  | Canadian Dollar     | C$     | 1.25          | North America |
| AUD  | Australian Dollar   | A$     | 1.35          | Oceania       |
| UGX  | Ugandan Shilling    | USh    | 3700.0        | Africa        |
| KES  | Kenyan Shilling     | KSh    | 129.0         | Africa        |
| TZS  | Tanzanian Shilling  | TSh    | 2300.0        | Africa        |
| NGN  | Nigerian Naira      | ₦      | 410.0         | Africa        |
| ZAR  | South African Rand  | R      | 15.0          | Africa        |
| GHS  | Ghanaian Cedi       | GH₵    | 6.0           | Africa        |
| RWF  | Rwandan Franc       | FRw    | 1000.0        | Africa        |
| INR  | Indian Rupee        | ₹      | 74.0          | Asia          |
| CNY  | Chinese Yuan        | ¥      | 6.45          | Asia          |
| SGD  | Singapore Dollar    | S$     | 1.35          | Asia          |
| HKD  | Hong Kong Dollar    | HK$    | 7.8           | Asia          |
| AED  | UAE Dirham          | د.إ    | 3.67          | Middle East   |
| SAR  | Saudi Riyal         | ﷼      | 3.75          | Middle East   |

**Default:** USD (is_default = true)
**All Active:** is_active = true

## Artisan Commands

### Migrate Tenant Databases
```bash
php artisan tenants:migrate
```
Creates the currencies table in all tenant databases.

### Seed Currencies for All Tenants
```bash
php artisan tenants:seed-currencies
```
Seeds all 20 currencies into each tenant database.

**File:** `app/Console/Commands/SeedTenantCurrencies.php`

## Views

### 1. Currency Index (`resources/views/settings/currencies/index.blade.php`)
**Route:** `/settings/currencies`

**Features:**
- Lists all currencies in a table
- Shows: name, code, symbol, exchange_rate, status (active/inactive), default badge
- Actions per row:
  - **Set as Default** button (hidden if already default)
  - **Edit** button
  - **Delete** button (hidden if default)
  - **Toggle Active** clickable badge (disabled if default)
- Empty state with "Add Currency" CTA
- Info alert explaining exchange rate reference

### 2. Currency Create (`resources/views/settings/currencies/create.blade.php`)
**Route:** `/settings/currencies/create`

**Form Fields:**
- **Currency Code** (3-letter ISO, e.g., USD) - required, maxlength=3
- **Currency Name** (e.g., US Dollar) - required, maxlength=255
- **Currency Symbol** (e.g., $) - required, maxlength=10
- **Exchange Rate (to USD)** - required, decimal(15,6), min=0.000001, max=999999999
- **Active** checkbox - default checked

**Actions:**
- Cancel → Back to index
- Create Currency → Submit form

### 3. Currency Edit (`resources/views/settings/currencies/edit.blade.php`)
**Route:** `/settings/currencies/{id}/edit`

**Same form as Create, with pre-filled values**

**Additional Logic:**
- If currency is default:
  - Active checkbox is disabled (cannot deactivate)
  - Blue alert shows "Default Currency" badge and explanation
- Info alert explains exchange rate reference

## Menu Integration

### Sidebar Menu (`resources/views/tenant/layouts/partials/admin-menu.blade.php`)
Added under Settings submenu:
```blade
<a href="{{ route('settings.currencies.index') }}">
    <span class="bi bi-currency-exchange me-2"></span>{{ __('Currencies') }}
</a>
```

### Settings Overview Page (`resources/views/settings/index.blade.php`)
Added currency card with:
- Icon: bi-currency-exchange
- Title: "Currencies"
- Description: "Manage currencies and exchange rates for payment processing."
- Badge: Finance
- Button: Manage → `/settings/currencies`

## Menu Rename: "Payment Gateway" → "Payment Settings"

### Files Updated (5 files, 10 occurrences)

1. **admin-menu.blade.php** - Sidebar menu item
2. **payments.blade.php** - Page title "Payment Settings"
3. **index.blade.php** - Settings overview card title
4. **general.blade.php** - Quick actions button
5. **academic.blade.php** - Quick actions button

## Usage Examples

### In Controllers
```php
use App\Models\Currency;

// Get default currency
$defaultCurrency = Currency::getDefault();

// Format payment amount
$formatted = $defaultCurrency->format(5000); // "$5,000.00"

// Convert fees from USD to UGX
$usd = Currency::where('code', 'USD')->first();
$ugx = Currency::where('code', 'UGX')->first();
$feeInUGX = $usd->convertTo(100, $ugx); // 370000.00
```

### In Blade Templates
```blade
{{-- Display formatted amount in default currency --}}
{{ formatMoney(1234.56) }}

{{-- Display with specific currency --}}
{{ formatMoney($amount, $currency) }}

{{-- Convert and display --}}
@php
    $ugxAmount = convertCurrency($usdAmount, 'USD', 'UGX');
@endphp
{{ formatMoney($ugxAmount, $ugxCurrency) }}
```

### In Payment Gateway Integration
```php
// Get school's default currency
$currency = currentCurrency();

// Format invoice total
$invoiceTotal = $currency->format($invoice->total);

// Convert to gateway's required currency (e.g., USD)
if ($currency->code !== 'USD') {
    $usd = Currency::where('code', 'USD')->first();
    $amountInUSD = $currency->convertTo($invoice->total, $usd);
} else {
    $amountInUSD = $invoice->total;
}
```

## Production Deployment Checklist

- [x] Currency migration created (tenant connection)
- [x] Currency model with all methods
- [x] Currency seeder with 20 currencies
- [x] CurrencyController with full CRUD
- [x] Routes registered (resource + custom)
- [x] Global helper functions (formatMoney, currentCurrency, convertCurrency)
- [x] Currency views (index, create, edit)
- [x] Sidebar menu integration
- [x] Settings overview card
- [x] Menu rename ("Payment Gateway" → "Payment Settings")
- [x] Migration run on all 4 tenant databases
- [x] Seeder run on all 4 tenant databases
- [x] Artisan command: tenants:seed-currencies

## Testing

### Test Currency CRUD
```bash
# Visit currency management
http://school.skolaris.test/settings/currencies

# Test operations:
1. View currencies list (should show 20 seeded currencies)
2. Create new currency (e.g., MXN - Mexican Peso)
3. Edit existing currency (change exchange rate)
4. Set currency as default (click star button)
5. Toggle active/inactive status
6. Delete non-default currency
7. Verify cannot delete default currency
```

### Test Helper Functions
```php
// In Tinker
php artisan tinker

// Test currentCurrency()
$currency = currentCurrency();
echo $currency->code; // "USD"

// Test formatMoney()
echo formatMoney(1234.56); // "$1,234.56"

// Test convertCurrency()
$ugxAmount = convertCurrency(100, 'USD', 'UGX');
echo $ugxAmount; // 370000.00
```

## Exchange Rate Updates

To update exchange rates in production:

1. **Manual Update via UI:**
   - Navigate to Settings → Currencies
   - Click Edit on currency
   - Update Exchange Rate field
   - Click Update Currency

2. **Bulk Update via Tinker:**
   ```php
   Currency::where('code', 'UGX')->update(['exchange_rate' => 3750.000000]);
   Currency::where('code', 'KES')->update(['exchange_rate' => 130.000000]);
   ```

3. **External API Integration (Future Enhancement):**
   - Create scheduled job to fetch latest rates from API
   - Update exchange rates daily/hourly
   - Recommended APIs: exchangerate-api.com, fixer.io

## 100% Production Ready ✅

All components implemented and tested:
- ✅ Database: Migration on tenant connection
- ✅ Model: Full business logic with 5 key methods
- ✅ Seeder: 20 major world currencies
- ✅ Controller: Full CRUD + 2 custom actions
- ✅ Routes: Resource + custom routes registered
- ✅ Views: Complete UI (index, create, edit)
- ✅ Helpers: 3 global functions for easy usage
- ✅ Menu: Integrated in sidebar and overview
- ✅ Migrations Run: All 4 tenant databases
- ✅ Seeding Complete: All 4 tenant databases
- ✅ Artisan Command: tenants:seed-currencies created
- ✅ Documentation: Complete technical reference

**Status:** 100% PRODUCTION READY 🎉

## Support

For questions or issues with currency management:
1. Check this documentation
2. Review code examples in controller/views
3. Test in development environment first
4. Contact development team for exchange rate API integration
