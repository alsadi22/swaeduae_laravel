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
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@swaeduae.ae'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        $adminUser->assignRole('admin');

        // Create a test volunteer user
        $volunteerUser = User::firstOrCreate(
            ['email' => 'volunteer@swaeduae.ae'],
            [
                'name' => 'Test Volunteer',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        $volunteerUser->assignRole('volunteer');

        // Create a test organization manager
        $orgManagerUser = User::firstOrCreate(
            ['email' => 'org@swaeduae.ae'],
            [
                'name' => 'Organization Manager',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        $orgManagerUser->assignRole('organization-manager');
    }
}