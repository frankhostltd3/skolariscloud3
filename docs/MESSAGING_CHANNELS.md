# Messaging Channels Configuration

## Overview

The Skolaris Cloud platform supports multiple messaging channels for communicating with students, parents, staff, and landlords. The messaging system is production-ready and supports SMS, WhatsApp, and Telegram channels with multiple provider options.

## Access Settings

**Admin Panel:** Settings → Messaging Channels  
**Direct URL:** `http://your-school.localhost:8000/settings/messaging`  
**Route Name:** `settings.messaging.edit`

## Supported Channels

### 1. SMS Messaging

Send school-wide alerts, parent notifications, and landlord announcements through SMS gateways.

#### Available Providers:

##### **Twilio SMS**
- **Purpose:** Global SMS delivery with scalable throughput
- **Configuration:**
  - Account SID
  - Auth Token (encrypted)
  - Default From Number
- **Environment Variables:**
  - `TWILIO_SMS_ACCOUNT_SID`
  - `TWILIO_SMS_AUTH_TOKEN`
  - `TWILIO_SMS_FROM`

##### **Vonage (Nexmo)**
- **Purpose:** SMS reach using Vonage Communications APIs
- **Configuration:**
  - API Key
  - API Secret (encrypted)
  - Sender ID / From Number
- **Environment Variables:**
  - `VONAGE_SMS_API_KEY`
  - `VONAGE_SMS_API_SECRET`
  - `VONAGE_SMS_FROM`

##### **Africa's Talking**
- **Purpose:** Regional SMS connectivity across African carriers
- **Configuration:**
  - Username
  - API Key (encrypted)
  - Sender ID / Shortcode
- **Environment Variables:**
  - `AFRICASTALKING_SMS_USERNAME`
  - `AFRICASTALKING_SMS_API_KEY`
  - `AFRICASTALKING_SMS_FROM`

##### **Custom SMS API**
- **Purpose:** Bring your own SMS provider with REST integration
- **Configuration:**
  - Provider Name
  - API Base URL
  - Access Key
  - Secret Key (encrypted)
  - Sender ID
  - Additional Metadata (JSON)
- **Environment Variables:**
  - `CUSTOM_SMS_ACCESS_KEY`
  - `CUSTOM_SMS_SECRET_KEY`
  - `CUSTOM_SMS_FROM`

---

### 2. WhatsApp Messaging

Reach guardians and community groups using WhatsApp Business APIs.

#### Available Providers:

##### **Twilio WhatsApp**
- **Purpose:** Send WhatsApp templates and session messages via Twilio
- **Configuration:**
  - Account SID
  - Auth Token (encrypted)
  - WhatsApp Sender (e.g., `whatsapp:+1234567890`)
- **Environment Variables:**
  - `TWILIO_WHATSAPP_ACCOUNT_SID`
  - `TWILIO_WHATSAPP_AUTH_TOKEN`
  - `TWILIO_WHATSAPP_FROM`

##### **Meta WhatsApp Cloud API**
- **Purpose:** Official WhatsApp Business Platform hosted by Meta
- **Configuration:**
  - Permanent Access Token (encrypted)
  - Phone Number ID
  - Business Account ID
  - Webhook Verify Token
  - Webhook URL
- **Environment Variables:**
  - `META_WHATSAPP_ACCESS_TOKEN`
  - `META_WHATSAPP_PHONE_NUMBER_ID`
  - `META_WHATSAPP_BUSINESS_ID`
  - `META_WHATSAPP_VERIFY_TOKEN`
  - `META_WHATSAPP_WEBHOOK_URL`

##### **Custom WhatsApp Provider**
- **Purpose:** Integrate alternative WhatsApp aggregators or gateways
- **Configuration:**
  - Provider Name
  - API Base URL
  - API Key (encrypted)
  - Sender / Phone Number
  - Additional Metadata (JSON)
- **Environment Variables:**
  - `CUSTOM_WHATSAPP_API_KEY`
  - `CUSTOM_WHATSAPP_SENDER`

---

### 3. Telegram Messaging

Send automated updates, alerts, and notifications through Telegram bots.

#### Available Providers:

##### **Telegram Bot API**
- **Purpose:** Official Telegram Bot API for broadcasting messages to channels and groups
- **Configuration:**
  - Bot Token (encrypted)
  - Bot Username
  - Default Chat/Channel ID
  - Webhook URL (optional)
  - Parse Mode (None, Markdown, MarkdownV2, HTML)
- **Environment Variables:**
  - `TELEGRAM_BOT_TOKEN`
  - `TELEGRAM_BOT_USERNAME`
  - `TELEGRAM_DEFAULT_CHAT_ID`
  - `TELEGRAM_WEBHOOK_URL`
  - `TELEGRAM_PARSE_MODE`
- **Default Parse Mode:** HTML

##### **Custom Telegram Provider**
- **Purpose:** Integrate third-party Telegram gateway or aggregator
- **Configuration:**
  - Provider Name
  - API Base URL
  - API Key (encrypted)
  - Bot ID / Identifier
  - Additional Metadata (JSON)
- **Environment Variables:**
  - `CUSTOM_TELEGRAM_API_KEY`
  - `CUSTOM_TELEGRAM_BOT_ID`

---

## Configuration Process

### Step 1: Enable Channel
1. Navigate to Settings → Messaging Channels
2. Expand the desired channel (SMS, WhatsApp, or Telegram)
3. Expand the provider you want to use
4. Toggle "Enable [Provider Name]" to ON

### Step 2: Configure Credentials
1. Fill in all required fields for the provider
2. Sensitive fields (tokens, secrets) are encrypted in the database
3. Leave password fields blank to keep existing values

### Step 3: Sync to .env (Production/Landlord Only)
1. If you're a super-admin/landlord with .env write access:
   - Check "Sync [Provider] credentials to .env"
   - This mirrors the credentials to your `.env` file
2. This option is only visible for landlords on central deployment

### Step 4: Save Settings
1. Click "Save settings" button
2. Settings are applied immediately via `MessagingConfigurator` service
3. Success message confirms the update

---

## Security Features

### Encryption
- All sensitive fields (tokens, passwords, secrets) are encrypted in the database
- Uses Laravel's `encrypted:array` casting for the `config` column

### Environment Sync
- `.env` synchronization is restricted to:
  - Super-admin/landlord users
  - Users without a `currentSchool` (central context)
  - When `.env` file is writable
- All sync operations are logged with user details and IP address

### Access Control
- Only users with `UserType::ADMIN` can access messaging settings
- Tenant-level isolation ensures schools can't access each other's settings

---

## Database Schema

**Table:** `messaging_channel_settings`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| channel | string(50) | Channel type: `sms`, `whatsapp`, `telegram` |
| provider | string(100) | Provider name: `twilio`, `vonage`, `telegram_bot`, etc. |
| is_enabled | boolean | Whether the provider is active |
| config | json (encrypted) | Provider-specific configuration (credentials, settings) |
| meta | json (encrypted) | Additional metadata |
| timestamps | timestamps | Created/updated timestamps |

**Unique Constraint:** `(channel, provider)` - One configuration per channel/provider combination

---

## Configuration Files

### config/messaging.php
Defines all available channels, providers, and their field definitions.

### app/Services/MessagingConfigurator.php
Applies saved settings to Laravel's runtime configuration.

### app/Services/EnvWriter.php
Safely updates `.env` file with new credentials (production use).

---

## Usage in Code

### Check if a Channel is Enabled

```php
use App\Models\MessagingChannelSetting;

$twilioSms = MessagingChannelSetting::query()
    ->where('channel', 'sms')
    ->where('provider', 'twilio')
    ->where('is_enabled', true)
    ->first();

if ($twilioSms) {
    $accountSid = $twilioSms->config['account_sid'] ?? null;
    $authToken = $twilioSms->config['auth_token'] ?? null;
    // Use credentials to send SMS
}
```

### Send SMS (Example with Twilio)

```php
use Twilio\Rest\Client;

$accountSid = config('services.twilio.sms.account_sid');
$authToken = config('services.twilio.sms.auth_token');
$fromNumber = config('services.twilio.sms.from');

if ($accountSid && $authToken) {
    $client = new Client($accountSid, $authToken);
    $client->messages->create(
        '+256700000000', // To number
        [
            'from' => $fromNumber,
            'body' => 'Hello from Skolaris Cloud!'
        ]
    );
}
```

### Send Telegram Message (Example)

```php
use Illuminate\Support\Facades\Http;

$botToken = config('services.telegram.bot_token');
$chatId = config('services.telegram.default_chat_id');
$parseMode = config('services.telegram.parse_mode', 'HTML');

if ($botToken && $chatId) {
    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
        'chat_id' => $chatId,
        'text' => '<b>Alert:</b> New student registration pending approval',
        'parse_mode' => $parseMode,
    ]);
}
```

---

## Production Deployment Checklist

- [ ] Configure at least one SMS provider for critical alerts
- [ ] Enable WhatsApp for parent communications (if applicable)
- [ ] Set up Telegram bot for staff notifications (optional)
- [ ] Test message delivery on all enabled channels
- [ ] Verify encryption is working (check database directly)
- [ ] Configure webhooks for two-way communication (WhatsApp, Telegram)
- [ ] Set up monitoring/logging for message delivery failures
- [ ] Document emergency fallback procedures
- [ ] Train admin staff on managing messaging settings
- [ ] Review and comply with local SMS/messaging regulations

---

## Troubleshooting

### "Environment file updates are enabled" alert not showing
- Check if user is landlord (not tenant context)
- Verify `.env` file exists and is writable
- Ensure user has `UserType::ADMIN` and `is_landlord` flag

### Messages not sending
- Verify provider is enabled in settings
- Check credentials are correct and not expired
- Review Laravel logs for API errors
- Ensure `MessagingConfigurator::apply()` was called after saving

### Credentials not syncing to .env
- Verify "Sync to .env" checkbox was checked
- Check server logs for write errors
- Ensure `.env` file permissions allow writes
- Verify you're logged in as landlord (not tenant)

### Password fields showing blank
- This is expected for security (concealed fields)
- Existing values are preserved when left blank
- Only enter new value if you need to change it

---

## API Reference

### Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/settings/messaging` | `settings.messaging.edit` | Display messaging settings form |
| PUT | `/settings/messaging` | `settings.messaging.update` | Save messaging settings |

### Middleware
- `auth` - Requires authenticated user
- `user.type:admin` - Requires admin user type

### Controllers
- `App\Http\Controllers\Settings\MessagingSettingsController`

### Models
- `App\Models\MessagingChannelSetting`

### Services
- `App\Services\MessagingConfigurator` - Applies settings to config
- `App\Services\EnvWriter` - Updates .env file

### Form Requests
- `App\Http\Requests\UpdateMessagingSettingsRequest` - Validates input

---

## Future Enhancements

- Email-to-SMS gateway integration
- Message queue and retry logic
- Delivery status tracking and reporting
- Template management for common messages
- Scheduled message campaigns
- Two-way messaging support
- Message history and audit trail
- Cost tracking per provider
- A/B testing for message effectiveness
- Integration with notification preferences

---

## Support

For technical support or feature requests, contact the development team or file an issue in the project repository.

**Last Updated:** November 15, 2025  
**Version:** 1.0.0
