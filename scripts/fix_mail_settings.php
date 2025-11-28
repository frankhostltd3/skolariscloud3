<?php

$school = App\Models\School::where('subdomain', 'victorianileschool')->first();

if ($school) {
    echo "Found school: " . $school->name . "\n";
    $manager = app(App\Services\TenantDatabaseManager::class);
    $manager->connect($school);

    $setting = App\Models\MailSetting::firstOrNew();
    $setting->mailer = 'log';
    $setting->save();

    echo "Updated mailer to 'log' for " . $school->name . "\n";
} else {
    echo "School 'victorianileschool' not found.\n";
}
