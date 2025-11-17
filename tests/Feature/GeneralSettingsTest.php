<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\User;
use Tests\TestCase;

class GeneralSettingsTest extends TestCase
{
    public function test_admin_can_access_general_settings_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('settings.general.edit'));

        $response->assertStatus(200);
        $response->assertSee('General Settings');
        $response->assertSee('School Information');
        $response->assertSee('Application Settings');
    }

    public function test_admin_can_update_school_information(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('settings.general.update'), [
                'form_type' => 'school_info',
                'school_name' => 'Test School',
                'school_code' => 'TST001',
                'school_email' => 'test@school.com',
                'school_phone' => '+256-123-456789',
                'school_address' => '123 Test Street',
                'school_website' => 'https://testschool.com',
                'principal_name' => 'Dr. Test Principal',
                'school_type' => 'private',
                'school_category' => 'day',
                'gender_type' => 'mixed',
            ]);

        $response->assertRedirect(route('settings.general.edit'));
        $response->assertSessionHas('status', 'School information updated successfully.');

        $this->assertEquals('Test School', setting('school_name'));
        $this->assertEquals('TST001', setting('school_code'));
    }

    public function test_admin_can_update_application_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('settings.general.update'), [
                'form_type' => 'application',
                'app_name' => 'Test App',
                'timezone' => 'Africa/Kampala',
                'date_format' => 'd/m/Y',
                'time_format' => 'g:i A',
                'default_language' => 'en',
                'records_per_page' => 25,
            ]);

        $response->assertRedirect(route('settings.general.edit'));
        $response->assertSessionHas('status', 'Application settings updated successfully.');

        $this->assertEquals('Test App', setting('app_name'));
        $this->assertEquals('Africa/Kampala', setting('timezone'));
        $this->assertEquals(25, (int) setting('records_per_page'));
    }

    public function test_non_admin_cannot_access_general_settings(): void
    {
        $user = User::factory()->create([
            'user_type' => UserType::STUDENT,
        ]);

        $response = $this->actingAs($user)
            ->get(route('settings.general.edit'));

        $response->assertStatus(403);
    }

    public function test_admin_can_clear_cache(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('settings.general.clear-cache'), [], [
                'Accept' => 'application/json',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }
}
