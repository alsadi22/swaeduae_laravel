<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);

        // Seed badges
        $this->call(BadgeSeeder::class);

        // Seed settings
        $this->call(SettingsSeeder::class);

        // Seed pages
        $this->call(PagesSeeder::class);

        // Create test users
        // User::factory(10)->create();

        // Create a test admin user
        // IMPORTANT: Change these passwords in production!
        // Default password for development: Admin@2025!Swaed
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@swaeduae.ae'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => bcrypt('Admin@2025!Swaed'),
                'unique_id' => 'ADMIN' . strtoupper(uniqid()),
            ]
        );
        $adminUser->assignRole('admin');

        // Create a test volunteer user
        // Default password for development: Volunteer@2025!Swaed
        $volunteerUser = User::firstOrCreate(
            ['email' => 'volunteer@swaeduae.ae'],
            [
                'name' => 'Test Volunteer',
                'email_verified_at' => now(),
                'password' => bcrypt('Volunteer@2025!Swaed'),
                'unique_id' => 'VOL' . strtoupper(uniqid()),
            ]
        );
        $volunteerUser->assignRole('volunteer');

        // Create a test organization manager
        // Default password for development: Org@2025!Swaed
        $orgManagerUser = User::firstOrCreate(
            ['email' => 'org@swaeduae.ae'],
            [
                'name' => 'Organization Manager',
                'email_verified_at' => now(),
                'password' => bcrypt('Org@2025!Swaed'),
                'unique_id' => 'ORG' . strtoupper(uniqid()),
            ]
        );
        $orgManagerUser->assignRole('organization-manager');
    }
}