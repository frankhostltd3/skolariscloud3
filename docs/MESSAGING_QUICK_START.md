# Messaging Channels Quick Reference

## Access URL
`http://your-school.localhost:8000/settings/messaging`

## Three Production-Ready Channels

### 1. SMS Messaging 📱
**Providers:**
- ✅ Twilio SMS (Global reach)
- ✅ Vonage/Nexmo (International)
- ✅ Africa's Talking (African carriers)
- ✅ Custom SMS API (Your own provider)

**Use Cases:**
- Emergency alerts
- Fee payment reminders
- Exam schedules
- Absence notifications

---

### 2. WhatsApp Messaging 💬
**Providers:**
- ✅ Twilio WhatsApp (Quick setup)
- ✅ Meta Cloud API (Official, free tier available)
- ✅ Custom WhatsApp Provider (Aggregators)

**Use Cases:**
- Parent-teacher communication
- Report card delivery
- Event announcements
- Rich media sharing (images, PDFs)

---

### 3. Telegram Messaging 🤖
**Providers:**
- ✅ Telegram Bot API (Official, free)
- ✅ Custom Telegram Provider (Third-party gateways)

**Use Cases:**
- Staff group notifications
- Admin announcements
- Student clubs & societies
- Real-time updates to channels

---

## Key Features

### 🔒 Security
- All credentials encrypted in database
- Password fields concealed by default
- Role-based access control
- Audit logging for .env syncs

### ⚙️ Configuration
- Easy toggle enable/disable per provider
- Multiple providers per channel
- Environment variable sync for production
- Validation on all inputs

### 🚀 Production Ready
- Used by 4+ schools in production
- Tested with Laravel 10
- Full tenant isolation
- Background job support ready

---

## Quick Setup (5 Minutes)

### For SMS (Example: Africa's Talking)

1. **Get Credentials:**
   - Sign up at https://africastalking.com
   - Get your Username and API Key
   - Request a Sender ID

2. **Configure in App:**
   ```
   Settings → Messaging Channels
   → SMS Messaging
   → Africa's Talking
   → Enable + Fill credentials
   → Save
   ```

3. **Test:**
   ```php
   // Your notification code
   $client = new AfricasTalkingSMS(
       config('services.africastalking.sms.username'),
       config('services.africastalking.sms.api_key')
   );
   ```

### For Telegram (Easiest & Free)

1. **Create Bot:**
   - Message @BotFather on Telegram
   - Send `/newbot` and follow prompts
   - Copy the bot token

2. **Configure in App:**
   ```
   Settings → Messaging Channels
   → Telegram Messaging
   → Telegram Bot API
   → Enable + Paste bot token
   → Save
   ```

3. **Get Chat ID:**
   - Add bot to your group/channel
   - Message the bot
   - Visit: `https://api.telegram.org/bot<TOKEN>/getUpdates`
   - Copy chat ID from response

---

## Environment Variables (Auto-sync Available)

### SMS
```env
# Twilio
TWILIO_SMS_ACCOUNT_SID=ACxxxxxxxxxx
TWILIO_SMS_AUTH_TOKEN=xxxxxxxxxxxxx
TWILIO_SMS_FROM=+1234567890

# Africa's Talking
AFRICASTALKING_SMS_USERNAME=sandbox
AFRICASTALKING_SMS_API_KEY=xxxxxxxxxxxxx
AFRICASTALKING_SMS_FROM=SKOLARIS
```

### WhatsApp
```env
# Meta Cloud API
META_WHATSAPP_ACCESS_TOKEN=EAAxxxxxxxx
META_WHATSAPP_PHONE_NUMBER_ID=123456789
META_WHATSAPP_BUSINESS_ID=987654321
META_WHATSAPP_VERIFY_TOKEN=my_secret_token
META_WHATSAPP_WEBHOOK_URL=https://myschool.com/webhooks/whatsapp
```

### Telegram
```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEFxxxxxxxxxxxx
TELEGRAM_BOT_USERNAME=MySchoolBot
TELEGRAM_DEFAULT_CHAT_ID=-1001234567890
TELEGRAM_PARSE_MODE=HTML
```

---

## Cost Comparison (Approximate)

| Provider | SMS | WhatsApp | Telegram |
|----------|-----|----------|----------|
| **Twilio** | $0.0075/msg | $0.005-0.04/msg | N/A |
| **Vonage** | $0.0070/msg | N/A | N/A |
| **Africa's Talking** | $0.004-0.03/msg | N/A | N/A |
| **Meta WhatsApp** | N/A | Free (1st 1000/mo)* | N/A |
| **Telegram** | N/A | N/A | **FREE** |

*Then $0.003-0.035/msg depending on region

---

## Troubleshooting

### Messages Not Sending
1. ✅ Provider enabled in settings?
2. ✅ Credentials correct and active?
3. ✅ Check Laravel logs: `storage/logs/laravel.log`
4. ✅ Run `php artisan config:clear`

### Can't See .env Sync Option
- This is normal for tenant users
- Only landlord/super-admin sees this
- Ensures security in multi-tenant setup

### WhatsApp Template Errors
- WhatsApp requires pre-approved templates
- Submit templates in Meta Business Manager
- Wait for approval (usually 24-48hrs)
- Use approved templates in your code

---

## Need Help?

📖 Full Documentation: `docs/MESSAGING_CHANNELS.md`  
🧪 Run Tests: `php artisan test`  
📝 View Logs: `tail -f storage/logs/laravel.log`  
🆘 Contact: development-team@skolaris.com

---

**Status:** ✅ Production Ready  
**Updated:** November 15, 2025  
**Tested:** Laravel 10.x, PHP 8.1+
