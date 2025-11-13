<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/frankhostltd3/skolariscloud3/actions/workflows/deploy.yml"><img src="https://github.com/frankhostltd3/skolariscloud3/actions/workflows/deploy.yml/badge.svg" alt="Deploy to VPS (CyberPanel)"></a>
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## SMATCAMPUS Tenant Onboarding

- Set `CENTRAL_DOMAIN` in your `.env` (e.g. `CENTRAL_DOMAIN=skolariscloud.com`) so the app can derive marketing and tenant URLs.
- Run database migrations (`php artisan migrate`) to add the `subdomain` column to `schools`.
- Create new schools from the central landing page (`/register`). Each school receives a unique subdomain and an administrator account.
- Staff, parents, and students should visit their school subdomain to register via invitation and to sign in.

## Email Delivery

- **PHP mail (default)** – Set `MAIL_MAILER=mail` in your `.env` and ensure the host's `mail()` function is enabled. Update `MAIL_FROM_ADDRESS` to a verified domain (e.g. `no-reply@your-domain.com`). Use this for quick smoke tests; production deployments should switch to an authenticated provider for better deliverability.
- **SMTP** – Switch to `MAIL_MAILER=smtp` and supply `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, and `MAIL_ENCRYPTION` (usually `tls`). Keep `MAIL_MAILER=failover` if you want Laravel to try SMTP first and fall back to PHP mail or logging gracefully.
- **Mailgun** – Set `MAIL_MAILER=mailgun` with `MAILGUN_DOMAIN`, `MAILGUN_SECRET`, `MAILGUN_ENDPOINT`, and `MAILGUN_SCHEME`. Verify your sending domain (SPF/DKIM) inside Mailgun so tenant notifications land in inboxes reliably.
- **Amazon SES** – Use `MAIL_MAILER=ses` (or `ses-v2`) along with your `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, and `AWS_DEFAULT_REGION`. Verify domains/identities inside SES for production sends.
- **Postmark** – Configure `MAIL_MAILER=postmark`, set `POSTMARK_TOKEN`, and optionally `POSTMARK_MESSAGE_STREAM_ID` for transactional vs broadcast streams.
- **SendGrid** – Set `MAIL_MAILER=sendgrid` and provide `SENDGRID_API_KEY`. Templates, categories, and webhooks are available through SendGrid’s dashboard.
- **Resend** – Use `MAIL_MAILER=resend` with `RESEND_API_KEY` for a lightweight transactional provider that includes event webhooks and domain management.

> Tip: Admins can update per-school mail credentials through the in-app Mail Settings page (`/settings/mail`), which stores encrypted provider keys without touching your `.env` file.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
