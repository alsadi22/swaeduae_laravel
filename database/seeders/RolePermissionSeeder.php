<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'verify users',
            
            // Organization Management
            'view organizations',
            'create organizations',
            'edit organizations',
            'delete organizations',
            'approve organizations',
            'reject organizations',
            'manage own organization',
            
            // Event Management
            'view events',
            'create events',
            'edit events',
            'delete events',
            'approve events',
            'reject events',
            'manage own events',
            'feature events',
            
            // Application Management
            'view applications',
            'create applications',
            'edit applications',
            'delete applications',
            'approve applications',
            'reject applications',
            'manage own applications',
            'check in volunteers',
            'check out volunteers',
            
            // Certificate Management
            'view certificates',
            'create certificates',
            'edit certificates',
            'delete certificates',
            'verify certificates',
            'issue certificates',
            
            // Badge Management
            'view badges',
            'create badges',
            'edit badges',
            'delete badges',
            'award badges',
            
            // Reports and Analytics
            'view reports',
            'export data',
            'view analytics',
            
            // System Administration
            'manage system settings',
            'manage roles',
            'manage permissions',
            'view logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin Role
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin Role
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'view users', 'create users', 'edit users', 'verify users',
            'view organizations', 'approve organizations', 'reject organizations',
            'view events', 'approve events', 'reject events', 'feature events',
            'view applications', 'approve applications', 'reject applications',
            'view certificates', 'verify certificates', 'issue certificates',
            'view badges', 'create badges', 'edit badges', 'award badges',
            'view reports', 'export data', 'view analytics',
        ]);

        // Organization Manager Role
        $orgManager = Role::firstOrCreate(['name' => 'organization-manager']);
        $orgManager->givePermissionTo([
            'manage own organization',
            'view events', 'create events', 'edit events', 'manage own events',
            'view applications', 'approve applications', 'reject applications', 'manage own applications',
            'check in volunteers', 'check out volunteers',
            'view certificates', 'create certificates', 'issue certificates',
            'view reports', 'view analytics',
        ]);

        // Organization Staff Role
        $orgStaff = Role::firstOrCreate(['name' => 'organization-staff']);
        $orgStaff->givePermissionTo([
            'view events', 'manage own events',
            'view applications', 'manage own applications',
            'check in volunteers', 'check out volunteers',
            'view certificates',
        ]);

        // Volunteer Role
        $volunteer = Role::firstOrCreate(['name' => 'volunteer']);
        $volunteer->givePermissionTo([
            'view events',
            'create applications',
            'view certificates',
            'view badges',
        ]);

        // Moderator Role
        $moderator = Role::firstOrCreate(['name' => 'moderator']);
        $moderator->givePermissionTo([
            'view users', 'verify users',
            'view organizations', 'approve organizations', 'reject organizations',
            'view events', 'approve events', 'reject events',
            'view applications',
            'view certificates', 'verify certificates',
            'view reports',
        ]);
    }
}
