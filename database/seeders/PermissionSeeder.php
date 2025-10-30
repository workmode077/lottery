<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view-dashboard-data',
            'admins',
            'admin-settings',
            'roles',
            'site-settings',
            'social-links',
            'sliders',
            'contact-enquiries',
            'faq',
            'enquiries',
            'banner-and-meta-tags',
            'policies',
            'locations',
        ];

        // Sync permissions
        collect($permissions)->each(
            fn($name) =>
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin'])
        );
        Permission::whereIn('name', Permission::pluck('name')->diff($permissions))->delete();

        // Sync Super Admin role
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'admin',
        ]);
        $superAdminRole->syncPermissions($permissions);

        Admin::first()?->assignRole($superAdminRole);
    }
}
