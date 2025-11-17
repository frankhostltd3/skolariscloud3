# Creating Landlord Admin Users

## Important: Understanding Landlord Users

This project uses **Spatie Laravel Permission with Teams support**. All roles and permissions must have a `tenant_id` (the team foreign key).

**For landlord users**, the system uses a special tenant called `'skolaris-root'` as the team ID. This ensures:
- Proper permission isolation between landlord and tenant users
- Landlord roles/permissions are stored with `tenant_id = 'skolaris-root'`
- The `TenantTeamResolver` automatically uses this for landlord context

⚠️ **Critical**: When creating landlord roles manually, you **must** specify `tenant_id: 'skolaris-root'` or the role creation will fail.

## Quick Reference

### Method 1: Using Artisan Command (Recommended)
```bash
php artisan landlord:create-user
```

Follow the interactive prompts to create a user.

### Method 1a: Using Command Options
```bash
php artisan landlord:create-user \
    --name="John Doe" \
    --email="admin@example.com" \
    --password="SecurePassword123"
```

### Method 2: Using Tinker (Manual)
```bash
php artisan tinker
```

Then run:
```php
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

// Create user on central connection
$user = User::on(config('tenancy.database.central_connection'))->create([
    'name' => 'John Doe',
    'email' => 'admin@example.com',
    'password' => Hash::make('SecurePassword123'),
    'email_verified_at' => now(),
]);

// Create or get landlord role (must use 'skolaris-root' tenant_id)
$role = Role::firstOrCreate(
    [
        'name' => 'landlord-admin', 
        'guard_name' => 'landlord',
        'tenant_id' => 'skolaris-root'
    ]
);

// Assign role and permissions
$user->assignRole($role);
$user->givePermissionTo('access landlord dashboard');

// Display user info
echo "✅ User created!\n";
echo "Email: {$user->email}\n";
echo "Login at: " . url('/landlord/login') . "\n";
```

### Method 3: Database Seeder

Create a seeder file:
```bash
php artisan make:seeder LandlordUserSeeder
```

Edit `database/seeders/LandlordUserSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class LandlordUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::on(config('tenancy.database.central_connection'))->firstOrCreate(
            ['email' => 'admin@skolariscloud.com'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $role = Role::firstOrCreate(
            [
                'name' => 'landlord-admin', 
                'guard_name' => 'landlord',
                'tenant_id' => 'skolaris-root'
            ]
        );

        $user->assignRole($role);
        $user->givePermissionTo('access landlord dashboard');

        $this->command->info('Landlord user created: ' . $user->email);
    }
}
```

Then run:
```bash
php artisan db:seed --class=LandlordUserSeeder
```

## Default Login URL

After creating a landlord user, login at:
```
http://your-domain.com/landlord/login
```

## Testing the Login

1. **Navigate to**: `/landlord/login`
2. **Enter credentials** you created
3. **Access dashboard**: `/landlord/dashboard`

## Common Issues

### Issue: "These credentials do not match our records"
**Solution**: 
- Ensure you created the user on the **central connection**
- Check the user has the correct **guard_name** for roles (`landlord`)
- Verify the password was hashed correctly

### Issue: "Access Denied" after login
**Solution**:
- Ensure the user has the `access landlord dashboard` permission
- Check the role is assigned with guard `landlord`
- Verify middleware is correctly checking `auth:landlord`

### Issue: Cannot find User model
**Solution**:
```bash
composer dump-autoload
```

## Security Best Practices

1. **Strong Passwords**: Minimum 12 characters with mixed case, numbers, symbols
2. **Email Verification**: Always set `email_verified_at` for admin users
3. **Unique Emails**: Each landlord user must have a unique email
4. **Role Assignment**: Always assign `landlord-admin` role
5. **Permission Check**: Verify `access landlord dashboard` permission exists

## Quick Test Script

Create `test-landlord-user.php` in project root:
```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

$email = 'test@example.com';
$password = 'Test123456!';

$user = User::on(config('tenancy.database.central_connection'))->create([
    'name' => 'Test Admin',
    'email' => $email,
    'password' => Hash::make($password),
    'email_verified_at' => now(),
]);

$role = Role::firstOrCreate([
    'name' => 'landlord-admin', 
    'guard_name' => 'landlord',
    'tenant_id' => 'skolaris-root'
]);
$user->assignRole($role);
$user->givePermissionTo('access landlord dashboard');

echo "✅ Landlord user created!\n";
echo "Email: {$email}\n";
echo "Password: {$password}\n";
echo "Login: " . url('/landlord/login') . "\n";
```

Run:
```bash
php test-landlord-user.php
```

## Command Help

View all options:
```bash
php artisan landlord:create-user --help
```

## Troubleshooting Commands

Check if user exists:
```bash
php artisan tinker
> User::on(config('tenancy.database.central_connection'))->where('email', 'admin@example.com')->first();
```

Check user roles:
```bash
php artisan tinker
> $user = User::on(config('tenancy.database.central_connection'))->where('email', 'admin@example.com')->first();
> $user->roles;
```

Check user permissions:
```bash
php artisan tinker
> $user = User::on(config('tenancy.database.central_connection'))->where('email', 'admin@example.com')->first();
> $user->getAllPermissions();
```

## Multiple Landlord Users

You can create multiple landlord administrators:
```bash
php artisan landlord:create-user --name="Admin 1" --email="admin1@example.com"
php artisan landlord:create-user --name="Admin 2" --email="admin2@example.com"
php artisan landlord:create-user --name="Admin 3" --email="admin3@example.com"
```

Each will have full access to the landlord dashboard.
