<?php

namespace Tests\Feature\Landlord;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\CentralTestCase;

class LandlordAuthenticationTest extends CentralTestCase
{

	public function test_landlord_login_page_is_accessible(): void
	{
		$response = $this->get(route('landlord.login.show'));

		$response->assertOk();
		$response->assertSeeText('Landlord access');
	}

	public function test_landlord_can_authenticate_with_valid_credentials(): void
	{
		$user = User::factory()->create([
			'email' => 'landlord@example.com',
			'password' => Hash::make('secret-password'),
		]);

		$this->provisionLandlordAccess($user);

		$response = $this->post(route('landlord.login.store'), [
			'email' => $user->email,
			'password' => 'secret-password',
		]);

		$response->assertRedirect(route('landlord.dashboard'));
		$this->assertAuthenticatedAs($user, 'landlord');
	}

	public function test_landlord_permission_is_granted_on_login(): void
	{
		$user = User::factory()->create([
			'email' => 'grant-permission@example.com',
			'password' => Hash::make('secret-password'),
		]);

		$response = $this->post(route('landlord.login.store'), [
			'email' => $user->email,
			'password' => 'secret-password',
		]);

		$response->assertRedirect(route('landlord.dashboard'));
		$this->assertAuthenticatedAs($user, 'landlord');

		$user->refresh();
		$this->assertTrue($user->hasPermissionTo('access landlord dashboard', 'landlord'));
	}

	public function test_landlord_cannot_authenticate_with_invalid_credentials(): void
	{
		$user = User::factory()->create();

		$response = $this->from(route('landlord.login.show'))->post(route('landlord.login.store'), [
			'email' => $user->email,
			'password' => 'wrong-password',
		]);

		$response->assertRedirect(route('landlord.login.show'));
		$response->assertSessionHasErrors('email');
		$this->assertGuest('landlord');
	}

	public function test_unauthenticated_users_are_redirected_from_dashboard(): void
	{
		$response = $this->get(route('landlord.dashboard'));

		$response->assertRedirect(route('landlord.login.show'));
	}

	public function test_authenticated_landlord_can_view_dashboard(): void
	{
		$user = User::factory()->create();

		$tenantId = (string) Str::uuid();

		DB::table('tenants')->insert([
			'id' => $tenantId,
			'data' => json_encode([
				'name' => 'Aurora Academy',
				'plan' => 'growth',
				'country' => 'ke',
			]),
			'created_at' => now(),
			'updated_at' => now(),
		]);

		DB::table('domains')->insert([
			'tenant_id' => $tenantId,
			'domain' => 'aurora.skolariscloud.test',
			'created_at' => now(),
			'updated_at' => now(),
		]);

		$this->provisionLandlordAccess($user);

		$this->actingAs($user, 'landlord');

		$response = $this->get(route('landlord.dashboard'));

		$response->assertOk();
		$response->assertSeeText('Landlord overview');
		$response->assertSeeText('Aurora Academy');
	}

	public function test_landlord_can_visit_sidebar_pages(): void
	{
		$user = User::factory()->create([
			'email' => 'sidebar@example.com',
			'password' => Hash::make('secret-password'),
		]);

		$this->post(route('landlord.login.store'), [
			'email' => $user->email,
			'password' => 'secret-password',
		]);

		$this->assertAuthenticatedAs($user, 'landlord');

		$routes = [
			route('landlord.tenants.index'),
			route('landlord.billing'),
			route('landlord.analytics'),
			route('landlord.settings'),
		];

		foreach ($routes as $route) {
			$response = $this->get($route);
			$response->assertOk();
		}
	}

	private function provisionLandlordAccess(User $user): void
	{
		$tenantId = config('app.landlord_team_id', 'skolaris-root');

		DB::table('tenants')->updateOrInsert(
			['id' => $tenantId],
			[
				'created_at' => now(),
				'updated_at' => now(),
				'data' => json_encode([
					'name' => 'Skolaris HQ',
					'plan' => 'growth',
					'country' => 'KE',
					'timezone' => 'Africa/Nairobi',
				]),
			]
		);

		/** @var PermissionRegistrar $registrar */
		$registrar = app(PermissionRegistrar::class);
		$registrar->setPermissionsTeamId($tenantId);

		$permission = Permission::query()->firstOrCreate([
			'tenant_id' => $tenantId,
			'name' => 'access landlord dashboard',
			'guard_name' => 'landlord',
		]);

		$user->givePermissionTo($permission);
	}
}
