<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null) {
        return \App\Models\Setting::get($key, $default);
    }
}

// =============================================================================
// SCHOOL INFORMATION SETTINGS
// =============================================================================

if (!function_exists('school_name')) {
    /**
     * Get the school name setting
     *
     * @return string
     */
    function school_name(): string {
        return setting('school_name', 'School Management System');
    }
}

if (!function_exists('school_code')) {
    /**
     * Get the school code setting
     *
     * @return string
     */
    function school_code(): string {
        return setting('school_code', 'SCH001');
    }
}

if (!function_exists('school_email')) {
    /**
     * Get the school email setting
     *
     * @return string
     */
    function school_email(): string {
        return setting('school_email', 'info@school.com');
    }
}

if (!function_exists('school_phone')) {
    /**
     * Get the school phone setting
     *
     * @return string
     */
    function school_phone(): string {
        return setting('school_phone', '+1-234-567-8900');
    }
}

if (!function_exists('school_address')) {
    /**
     * Get the school address setting
     *
     * @return string
     */
    function school_address(): string {
        return setting('school_address', '123 Education Street, Learning City, State 12345');
    }
}

if (!function_exists('school_website')) {
    /**
     * Get the school website setting
     *
     * @return string
     */
    function school_website(): string {
        return setting('school_website', 'https://www.school.com');
    }
}

if (!function_exists('school_logo')) {
    /**
     * Get the school logo path setting
     *
     * @return string|null
     */
    function school_logo(): ?string {
        return setting('school_logo', null);
    }
}

if (!function_exists('school_motto')) {
    /**
     * Get the school motto setting
     *
     * @return string
     */
    function school_motto(): string {
        return setting('school_motto', 'Excellence in Education');
    }
}

if (!function_exists('principal_name')) {
    /**
     * Get the principal name setting
     *
     * @return string
     */
    function principal_name(): string {
        return setting('principal_name', 'Dr. Jane Smith');
    }
}

if (!function_exists('school_type')) {
    /**
     * Get the school type setting
     *
     * @return string
     */
    function school_type(): string {
        return setting('school_type', 'private');
    }
}

if (!function_exists('school_category')) {
    /**
     * Get the school category setting
     *
     * @return string
     */
    function school_category(): string {
        return setting('school_category', 'day');
    }
}

if (!function_exists('gender_type')) {
    /**
     * Get the gender type setting
     *
     * @return string
     */
    function gender_type(): string {
        return setting('gender_type', 'mixed');
    }
}

// School type convenience functions
if (!function_exists('is_government_school')) {
    /**
     * Check if the school is a government school
     *
     * @return bool
     */
    function is_government_school(): bool {
        return school_type() === 'government';
    }
}

if (!function_exists('is_private_school')) {
    /**
     * Check if the school is a private school
     *
     * @return bool
     */
    function is_private_school(): bool {
        return school_type() === 'private';
    }
}

if (!function_exists('is_hybrid_school')) {
    /**
     * Check if the school is a hybrid school
     *
     * @return bool
     */
    function is_hybrid_school(): bool {
        return school_type() === 'hybrid';
    }
}

// School category convenience functions
if (!function_exists('is_day_school')) {
    /**
     * Check if the school is a day school
     *
     * @return bool
     */
    function is_day_school(): bool {
        return school_category() === 'day';
    }
}

if (!function_exists('is_boarding_school')) {
    /**
     * Check if the school is a boarding school
     *
     * @return bool
     */
    function is_boarding_school(): bool {
        return school_category() === 'boarding';
    }
}

if (!function_exists('is_hybrid_category_school')) {
    /**
     * Check if the school is a hybrid category school
     *
     * @return bool
     */
    function is_hybrid_category_school(): bool {
        return school_category() === 'hybrid';
    }
}

// Gender type convenience functions
if (!function_exists('is_boys_school')) {
    /**
     * Check if the school is a boys only school
     *
     * @return bool
     */
    function is_boys_school(): bool {
        return gender_type() === 'boys';
    }
}

if (!function_exists('is_girls_school')) {
    /**
     * Check if the school is a girls only school
     *
     * @return bool
     */
    function is_girls_school(): bool {
        return gender_type() === 'girls';
    }
}

if (!function_exists('is_mixed_school')) {
    /**
     * Check if the school is a mixed gender school
     *
     * @return bool
     */
    function is_mixed_school(): bool {
        return gender_type() === 'mixed';
    }
}

// =============================================================================
// APPLICATION SETTINGS
// =============================================================================

if (!function_exists('app_name')) {
    /**
     * Get the application name setting
     *
     * @return string
     */
    function app_name(): string {
        return setting('app_name', config('app.name', 'SkolarisCloud'));
    }
}

if (!function_exists('academic_year_start')) {
    /**
     * Get the academic year start setting
     *
     * @return string
     */
    function academic_year_start(): string {
        return setting('academic_year_start', '2024-09-01');
    }
}

if (!function_exists('academic_year_end')) {
    /**
     * Get the academic year end setting
     *
     * @return string
     */
    function academic_year_end(): string {
        return setting('academic_year_end', '2025-06-30');
    }
}

if (!function_exists('timezone')) {
    /**
     * Get the timezone setting
     *
     * @return string
     */
    function timezone(): string {
        return setting('timezone', 'UTC');
    }
}

if (!function_exists('date_format')) {
    /**
     * Get the date format setting
     *
     * @return string
     */
    function date_format(): string {
        return setting('date_format', 'Y-m-d');
    }
}

if (!function_exists('time_format')) {
    /**
     * Get the time format setting
     *
     * @return string
     */
    function time_format(): string {
        return setting('time_format', 'H:i');
    }
}

if (!function_exists('currency')) {
    /**
     * Get the currency setting
     *
     * @return string
     */
    function currency(): string {
        return setting('currency', 'USD');
    }
}

if (!function_exists('language')) {
    /**
     * Get the language setting
     *
     * @return string
     */
    function language(): string {
        return setting('language', 'en');
    }
}

if (!function_exists('default_language')) {
    /**
     * Get the default language setting
     *
     * @return string
     */
    function default_language(): string {
        return setting('default_language', 'en');
    }
}

if (!function_exists('records_per_page')) {
    /**
     * Get the records per page setting
     *
     * @return int
     */
    function records_per_page(): int {
        return (int) setting('records_per_page', 15);
    }
}

// =============================================================================
// EMAIL SETTINGS
// =============================================================================

if (!function_exists('mail_driver')) {
    /**
     * Get the mail driver setting
     *
     * @return string
     */
    function mail_driver(): string {
        return setting('mail_driver', 'smtp');
    }
}

if (!function_exists('mail_host')) {
    /**
     * Get the mail host setting
     *
     * @return string
     */
    function mail_host(): string {
        return setting('mail_host', 'smtp.gmail.com');
    }
}

if (!function_exists('mail_port')) {
    /**
     * Get the mail port setting
     *
     * @return int
     */
    function mail_port(): int {
        return (int) setting('mail_port', 587);
    }
}

if (!function_exists('mail_encryption')) {
    /**
     * Get the mail encryption setting
     *
     * @return string|null
     */
    function mail_encryption(): ?string {
        return setting('mail_encryption', 'tls');
    }
}

if (!function_exists('mail_username')) {
    /**
     * Get the mail username setting
     *
     * @return string|null
     */
    function mail_username(): ?string {
        return setting('mail_username', null);
    }
}

if (!function_exists('mail_password')) {
    /**
     * Get the mail password setting
     *
     * @return string|null
     */
    function mail_password(): ?string {
        return setting('mail_password', null);
    }
}

if (!function_exists('mail_from_address')) {
    /**
     * Get the mail from address setting
     *
     * @return string
     */
    function mail_from_address(): string {
        return setting('mail_from_address', 'noreply@school.com');
    }
}

if (!function_exists('mail_from_name')) {
    /**
     * Get the mail from name setting
     *
     * @return string
     */
    function mail_from_name(): string {
        return setting('mail_from_name', 'School Management System');
    }
}

// =============================================================================
// ACADEMIC SETTINGS
// =============================================================================

if (!function_exists('grading_scale')) {
    /**
     * Get the grading scale setting
     *
     * @return string
     */
    function grading_scale(): string {
        return setting('grading_scale', 'percentage');
    }
}

if (!function_exists('passing_grade')) {
    /**
     * Get the passing grade setting
     *
     * @return float
     */
    function passing_grade(): float {
        return (float) setting('passing_grade', 50.0);
    }
}

if (!function_exists('class_system')) {
    /**
     * Get the class system setting
     *
     * @return string
     */
    function class_system(): string {
        return setting('class_system', 'standard');
    }
}

if (!function_exists('current_academic_year')) {
    /**
     * Get the current academic year setting
     *
     * @return string
     */
    function current_academic_year(): string {
        return setting('current_academic_year', '2024-2025');
    }
}

if (!function_exists('semester_system')) {
    /**
     * Get the semester system setting
     *
     * @return string
     */
    function semester_system(): string {
        return setting('semester_system', 'semester');
    }
}

if (!function_exists('attendance_marking')) {
    /**
     * Get the attendance marking setting
     *
     * @return string
     */
    function attendance_marking(): string {
        return setting('attendance_marking', 'automatic');
    }
}

if (!function_exists('minimum_attendance')) {
    /**
     * Get the minimum attendance setting
     *
     * @return int
     */
    function minimum_attendance(): int {
        return (int) setting('minimum_attendance', 75);
    }
}

if (!function_exists('late_arrival_grace')) {
    /**
     * Get the late arrival grace period setting (minutes)
     *
     * @return int
     */
    function late_arrival_grace(): int {
        return (int) setting('late_arrival_grace', 15);
    }
}

if (!function_exists('attendance_notifications')) {
    /**
     * Get the attendance notifications setting
     *
     * @return string
     */
    function attendance_notifications(): string {
        return setting('attendance_notifications', 'enabled');
    }
}

// Assignment reminder settings
if (!function_exists('assignment_reminder_windows')) {
    /**
     * Get the assignment reminder windows (hours)
     *
     * @return array
     */
    function assignment_reminder_windows(): array {
        $windows = setting('assignments.reminders.windows', '24');
        return array_map('intval', explode(',', $windows));
    }
}

if (!function_exists('assignment_reminders_auto_send_sms')) {
    /**
     * Check if assignment reminders should auto-send SMS
     *
     * @return bool
     */
    function assignment_reminders_auto_send_sms(): bool {
        return (bool) setting('assignments.reminders.auto_send.sms', false);
    }
}

if (!function_exists('assignment_reminders_auto_send_whatsapp')) {
    /**
     * Check if assignment reminders should auto-send WhatsApp
     *
     * @return bool
     */
    function assignment_reminders_auto_send_whatsapp(): bool {
        return (bool) setting('assignments.reminders.auto_send.whatsapp', false);
    }
}

if (!function_exists('assignment_reminders_log_only')) {
    /**
     * Check if assignment reminders should only be logged
     *
     * @return bool
     */
    function assignment_reminders_log_only(): bool {
        return (bool) setting('assignments.reminders.log_only', true);
    }
}

// =============================================================================
// SYSTEM SETTINGS
// =============================================================================

if (!function_exists('kiosk_allowed_tokens')) {
    /**
     * Get the kiosk allowed tokens
     *
     * @return array
     */
    function kiosk_allowed_tokens(): array {
        return (array) setting('kiosk.allowed_tokens', []);
    }
}

if (!function_exists('kiosk_require_token')) {
    /**
     * Check if kiosk requires token
     *
     * @return bool
     */
    function kiosk_require_token(): bool {
        return (bool) setting('kiosk.require_token', true);
    }
}

if (!function_exists('enabled_currencies')) {
    /**
     * Get the enabled currencies
     *
     * @return array
     */
    function enabled_currencies(): array {
        return (array) setting('enabled_currencies', ['USD']);
    }
}

if (!function_exists('fx_auto_enabled')) {
    /**
     * Check if automatic FX refresh is enabled
     *
     * @return bool
     */
    function fx_auto_enabled(): bool {
        return (bool) setting('fx_auto_enabled', true);
    }
}

// =============================================================================
// HR SETTINGS
// =============================================================================

if (!function_exists('hr_leave_integration_enabled')) {
    /**
     * Check if HR leave integration is enabled
     *
     * @return bool
     */
    function hr_leave_integration_enabled(): bool {
        return (bool) setting('hr_leave_integration_enabled', false);
    }
}

// =============================================================================
// NOTIFICATION SETTINGS
// =============================================================================

if (!function_exists('sms_provider')) {
    /**
     * Get the SMS provider setting
     *
     * @return string
     */
    function sms_provider(): string {
        return setting('sms.provider', 'log');
    }
}

if (!function_exists('sms_default_country_code')) {
    /**
     * Get the SMS default country code setting
     *
     * @return string
     */
    function sms_default_country_code(): string {
        return setting('sms.default_country_code', '+1');
    }
}

if (!function_exists('whatsapp_provider')) {
    /**
     * Get the WhatsApp provider setting
     *
     * @return string
     */
    function whatsapp_provider(): string {
        return setting('whatsapp.provider', 'log');
    }
}

if (!function_exists('notification_rate_per_recipient_per_min')) {
    /**
     * Get the notification rate limit per recipient per minute
     *
     * @return int
     */
    function notification_rate_per_recipient_per_min(): int {
        return (int) setting('notifications.rate.per_recipient_per_min', 10);
    }
}

if (!function_exists('notification_rate_per_admin_per_day')) {
    /**
     * Get the notification rate limit per admin per day
     *
     * @return int
     */
    function notification_rate_per_admin_per_day(): int {
        return (int) setting('notifications.rate.per_admin_per_day', 1000);
    }
}

// =============================================================================
// PAYMENT SETTINGS
// =============================================================================

if (!function_exists('mtn_api_user')) {
    /**
     * Get the MTN API user setting
     *
     * @return string|null
     */
    function mtn_api_user(): ?string {
        return setting('payments.mtn.api_user', null);
    }
}

if (!function_exists('mtn_api_key')) {
    /**
     * Get the MTN API key setting
     *
     * @return string|null
     */
    function mtn_api_key(): ?string {
        return setting('payments.mtn.api_key', null);
    }
}

if (!function_exists('mtn_primary_key')) {
    /**
     * Get the MTN primary key setting
     *
     * @return string|null
     */
    function mtn_primary_key(): ?string {
        return setting('payments.mtn.primary_key', null);
    }
}

if (!function_exists('mtn_callback_url')) {
    /**
     * Get the MTN callback URL setting
     *
     * @return string|null
     */
    function mtn_callback_url(): ?string {
        return setting('payments.mtn.callback_url', null);
    }
}

if (!function_exists('airtel_client_id')) {
    /**
     * Get the Airtel client ID setting
     *
     * @return string|null
     */
    function airtel_client_id(): ?string {
        return setting('payments.airtel.client_id', null);
    }
}

if (!function_exists('airtel_client_secret')) {
    /**
     * Get the Airtel client secret setting
     *
     * @return string|null
     */
    function airtel_client_secret(): ?string {
        return setting('payments.airtel.client_secret', null);
    }
}

if (!function_exists('airtel_callback_url')) {
    /**
     * Get the Airtel callback URL setting
     *
     * @return string|null
     */
    function airtel_callback_url(): ?string {
        return setting('payments.airtel.callback_url', null);
    }
}

if (!function_exists('stripe_secret')) {
    /**
     * Get the Stripe secret key setting
     *
     * @return string|null
     */
    function stripe_secret(): ?string {
        return setting('payments.stripe.secret', null);
    }
}

if (!function_exists('stripe_key')) {
    /**
     * Get the Stripe publishable key setting
     *
     * @return string|null
     */
    function stripe_key(): ?string {
        return setting('payments.stripe.key', null);
    }
}

if (!function_exists('stripe_webhook_secret')) {
    /**
     * Get the Stripe webhook secret setting
     *
     * @return string|null
     */
    function stripe_webhook_secret(): ?string {
        return setting('payments.stripe.webhook_secret', null);
    }
}

if (!function_exists('paypal_client_id')) {
    /**
     * Get the PayPal client ID setting
     *
     * @return string|null
     */
    function paypal_client_id(): ?string {
        return setting('payments.paypal.client_id', null);
    }
}

if (!function_exists('paypal_client_secret')) {
    /**
     * Get the PayPal client secret setting
     *
     * @return string|null
     */
    function paypal_client_secret(): ?string {
        return setting('payments.paypal.client_secret', null);
    }
}

if (!function_exists('paypal_webhook_id')) {
    /**
     * Get the PayPal webhook ID setting
     *
     * @return string|null
     */
    function paypal_webhook_id(): ?string {
        return setting('payments.paypal.webhook_id', null);
    }
}

if (!function_exists('account_status')) {
    /**
     * Get the account status setting
     *
     * @return string
     */
    function account_status(): string {
        return setting('account_status', 'verified');
    }
}

if (!function_exists('is_account_verified')) {
    /**
     * Check if the account is verified
     *
     * @return bool
     */
    function is_account_verified(): bool {
        return account_status() === 'verified';
    }
}

if (!function_exists('is_account_unverified')) {
    /**
     * Check if the account is unverified
     *
     * @return bool
     */
    function is_account_unverified(): bool {
        return account_status() === 'unverified';
    }
}

if (!function_exists('enable_two_factor_auth')) {
    /**
     * Get the two-factor authentication setting
     *
     * @return bool
     */
    function enable_two_factor_auth(): bool {
        return (bool) setting('enable_two_factor_auth', false);
    }
}

if (!function_exists('is_two_factor_enabled')) {
    /**
     * Check if two-factor authentication is enabled
     *
     * @return bool
     */
    function is_two_factor_enabled(): bool {
        return enable_two_factor_auth();
    }
}