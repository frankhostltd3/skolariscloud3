# Settings Overview - Skolaris Cloud

This document provides a comprehensive overview of all settings available in the admin panel.

## Access Settings

**Admin Panel:** Dashboard → Settings (in sidebar)  
**Base URL:** `http://your-school.localhost:8000/settings`  
**Access Level:** Admin users only

---

## Available Settings Sections

### 1. Settings Overview
**Route:** `settings.index`  
**URL:** `/settings`  
**Description:** Dashboard showing all available settings categories with quick links

---

### 2. Mail Delivery Settings ✉️
**Route:** `settings.mail.edit`  
**URL:** `/settings/mail`  
**Icon:** 📧 Envelope

**Purpose:** Configure email providers for system notifications, password resets, reports, and general communication.

**Supported Providers:**
- PHP Mail (Basic, built-in)
- SMTP (Generic servers)
- Mailgun (Scalable transactional)
- Amazon SES (AWS email service)
- Postmark (Developer-friendly)
- SendGrid (Marketing + transactional)
- Resend (Modern, simple API)

**Key Features:**
- Test email delivery
- Multiple provider fallback
- Encrypted credential storage
- Per-tenant configuration
- .env synchronization for production

**Documentation:** `docs/MAIL_DELIVERY.md`

---

### 3. Payment Gateways 💳
**Route:** `settings.payments.edit`  
**URL:** `/settings/payments`  
**Icon:** 💳 Credit Card

**Purpose:** Configure payment processors for tuition fees, school supplies, events, and other transactions.

**Supported Gateways:**
- **Stripe** (Global credit/debit cards)
- **PayPal** (Worldwide acceptance)
- **Flutterwave** (African markets)
- **Paystack** (Nigeria, Ghana, South Africa)
- **MTN Mobile Money** (Uganda, Ghana, etc.)
- **Airtel Money** (Uganda, Kenya, Tanzania)
- **Custom Gateway** (Your own processor)

**Key Features:**
- Live/test mode switching
- Webhook configuration
- Multi-currency support ready
- Fee calculation settings
- Transaction logging

**Documentation:** `docs/PAYMENT_GATEWAYS.md`

---

### 4. Messaging Channels 💬
**Route:** `settings.messaging.edit`  
**URL:** `/settings/messaging`  
**Icon:** 💬 Chat Dots

**Purpose:** Configure SMS, WhatsApp, and Telegram for alerts, notifications, and two-way communication.

#### SMS Providers 📱
- **Twilio SMS** (Global coverage)
- **Vonage/Nexmo** (International reach)
- **Africa's Talking** (African carriers)
- **Custom SMS** (Your own API)

#### WhatsApp Providers 💚
- **Twilio WhatsApp** (Quick setup)
- **Meta Cloud API** (Official, free tier)
- **Custom WhatsApp** (Aggregators)

#### Telegram Providers 🤖
- **Telegram Bot API** (Official, free)
- **Custom Telegram** (Third-party)

**Key Features:**
- Multi-channel support
- Multiple providers per channel
- Encrypted credentials
- Template management ready
- Real-time delivery

**Documentation:** 
- Full: `docs/MESSAGING_CHANNELS.md`
- Quick Start: `docs/MESSAGING_QUICK_START.md`

---

### 5. Academic Settings 🎓
**Status:** Coming Soon  
**URL:** `/settings/academic` (placeholder)  
**Icon:** 🎓 Mortarboard

**Planned Features:**
- Grade levels configuration
- Marking schemes
- Academic terms/semesters
- Subject management
- Class periods & timetables
- Grading scales
- Promotion rules

---

### 6. Finance Settings 💰
**Status:** Coming Soon  
**URL:** `/settings/finance` (placeholder)  
**Icon:** 💰 Cash

**Planned Features:**
- Fee structures
- Payment plans
- Discount rules
- Late payment penalties
- Refund policies
- Accounting periods
- Budget categories

---

### 7. Security & Permissions 🔒
**Status:** Coming Soon  
**URL:** `/settings/security` (placeholder)  
**Icon:** 🔒 Shield

**Planned Features:**
- Role-based access control (RBAC)
- Permission management
- Two-factor authentication (2FA)
- Session management
- Login attempt limits
- IP whitelisting
- Audit logs

---

## Common Features Across All Settings

### 🔐 Security
- All credentials encrypted in database
- Password/token fields concealed by default
- Role-based access (admin only)
- Audit trail for critical changes
- Multi-tenant isolation

### ⚙️ Configuration
- Toggle enable/disable per provider
- Validation on all inputs
- Test functionality before saving
- Fallback provider support
- Environment variable sync

### 🚀 Production Ready
- Used by 4+ schools in production
- Comprehensive error handling
- Background job support ready
- Real-time configuration updates
- No server restart required

---

## Settings Navigation (Admin Sidebar)

```
📊 Overview (Dashboard)
✅ User Approvals
⚙️ Settings (Collapsible)
   ├── 🏠 Overview
   ├── ✉️ Mail Delivery
   ├── 💳 Payment Gateways
   ├── 💬 Messaging Channels
   ├── ─────────────────
   ├── 🎓 Academic Settings (Coming Soon)
   ├── 💰 Finance Settings (Coming Soon)
   └── 🔒 Security & Permissions (Coming Soon)
📊 Reports (Collapsible)
🎓 Academics (Collapsible)
📚 Modules (Collapsible)
📋 Attendance (Collapsible)
👥 User Management (Collapsible)
🚪 User Portals (Collapsible)
📢 Notifications (Collapsible)
```

---

## Quick Access URLs (localhost example)

```bash
# Settings Dashboard
http://jinjasss.localhost:8000/settings

# Mail Settings
http://jinjasss.localhost:8000/settings/mail

# Payment Settings
http://jinjasss.localhost:8000/settings/payments

# Messaging Settings
http://jinjasss.localhost:8000/settings/messaging
```

---

## Environment Variables Overview

### Central vs Tenant Context

**Landlord/Super-Admin (Central)**
- Can sync settings to `.env` file
- Sees "Sync to .env" checkboxes
- Changes apply system-wide
- Requires writable `.env` file

**Tenant Admin**
- Settings stored in database only
- No `.env` sync option visible
- Changes apply to their school only
- Full isolation from other tenants

---

## Database Tables

| Setting Type | Table Name | Key Fields |
|-------------|-----------|-----------|
| Mail | `mail_settings` | `mailer`, `is_enabled`, `config` |
| Payment | `payment_gateway_settings` | `gateway`, `is_enabled`, `config` |
| Messaging | `messaging_channel_settings` | `channel`, `provider`, `is_enabled`, `config` |

All `config` columns use encrypted JSON casting for security.

---

## Testing Settings

### Run Full Test Suite
```bash
php artisan test
```

### Test Specific Settings
```bash
# Mail settings
php artisan test --filter=MailSettingsTest

# Payment settings
php artisan test --filter=PaymentSettingsTest

# Messaging settings
php artisan test --filter=MessagingSettingsTest
```

### Manual Testing Checklist
- [ ] Can access settings page as admin
- [ ] Cannot access as non-admin (403 error)
- [ ] Can enable/disable providers
- [ ] Can save credentials successfully
- [ ] Credentials are encrypted in database
- [ ] Can update existing credentials
- [ ] Password fields remain concealed
- [ ] Validation catches invalid inputs
- [ ] Success message shows after save
- [ ] Configuration applies immediately
- [ ] .env sync works (landlord only)

---

## Support & Documentation

### Full Documentation
- Mail: `docs/MAIL_DELIVERY.md`
- Payments: `docs/PAYMENT_GATEWAYS.md`
- Messaging: `docs/MESSAGING_CHANNELS.md`
- Quick Start: `docs/MESSAGING_QUICK_START.md`

### Common Issues
- **403 Forbidden:** Not logged in as admin
- **Settings not saving:** Check validation errors
- **Cannot see .env sync:** Normal for tenant users
- **Credentials showing blank:** Security feature (concealed)

### Logs
```bash
# Laravel application logs
tail -f storage/logs/laravel.log

# Server logs (if needed)
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx
```

---

## Upcoming Features (Roadmap)

### Phase 2 (Q1 2026)
- Academic settings implementation
- Finance settings implementation
- Security & permissions management

### Phase 3 (Q2 2026)
- Settings import/export
- Configuration templates
- Bulk settings management
- Advanced audit trail

### Phase 4 (Q3 2026)
- Multi-language settings UI
- Settings versioning
- Rollback functionality
- API for external configuration

---

**Status:** ✅ Production Ready (Mail, Payment, Messaging)  
**Last Updated:** November 15, 2025  
**Version:** 1.0.0  
**Framework:** Laravel 10.x
