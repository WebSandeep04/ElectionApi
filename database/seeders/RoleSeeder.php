<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, seed all permissions
        $this->call(PermissionSeeder::class);

        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Department manager with elevated permissions',
                'is_active' => true,
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Standard employee with basic permissions',
                'is_active' => true,
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Read-only access to system data',
                'is_active' => true,
            ],
            [
                'name' => 'guest',
                'display_name' => 'Guest',
                'description' => 'Limited access for external users',
                'is_active' => false,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        // Assign permissions to roles
        $this->assignRolePermissions();
    }

    private function assignRolePermissions(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $employeeRole = Role::where('name', 'employee')->first();
        $viewerRole = Role::where('name', 'viewer')->first();
        $guestRole = Role::where('name', 'guest')->first();

        // Admin gets all permissions
        if ($adminRole) {
            $allPermissions = Permission::all()->pluck('id')->toArray();
            $adminRole->permissions()->sync($allPermissions);
        }

        // Manager permissions
        if ($managerRole) {
            $managerPermissions = [
                'view_dashboard',
                'view_employee_management', 'manage_employees',
                'view_caste_management', 'view_caste_ratio', 'manage_cast_ratios',
                'view_village_description',
                'view_education_management',
                'view_category_management',
                'view_employee_types',
                'view_user_management', 'manage_users',
                'view_role_management',
                'view_parliament_management',
                'view_lok_sabha', 'manage_lok_sabha',
                'view_vidhan_sabha', 'manage_vidhan_sabha',
                'view_blocks', 'manage_blocks',
                'view_panchayats', 'manage_panchayats',
                'view_villages', 'manage_villages',
                'view_booths', 'manage_booths',
                'view_form_builder', 'manage_form_builder',
                'view_form_list', 'manage_forms',
                'view_respondent_table',
                'view_teams',
                'view_employee_analysis', 'view_analysis',
            ];
            $managerPermissionIds = Permission::whereIn('name', $managerPermissions)->pluck('id')->toArray();
            $managerRole->permissions()->sync($managerPermissionIds);
        }

        // Employee permissions
        if ($employeeRole) {
            $employeePermissions = [
                'view_dashboard',
                'view_employee_management',
                'view_caste_management', 'view_caste_ratio',
                'view_village_description',
                'view_education_management',
                'view_category_management',
                'view_employee_types',
                'view_parliament_management',
                'view_lok_sabha',
                'view_vidhan_sabha',
                'view_blocks',
                'view_panchayats',
                'view_villages',
                'view_booths',
                'view_form_list',
                'view_respondent_table',
                'view_teams',
                'view_employee_analysis',
            ];
            $employeePermissionIds = Permission::whereIn('name', $employeePermissions)->pluck('id')->toArray();
            $employeeRole->permissions()->sync($employeePermissionIds);
        }

        // Viewer permissions (read-only)
        if ($viewerRole) {
            $viewerPermissions = [
                'view_dashboard',
                'view_employee_management',
                'view_caste_management', 'view_caste_ratio',
                'view_village_description',
                'view_education_management',
                'view_category_management',
                'view_employee_types',
                'view_parliament_management',
                'view_lok_sabha',
                'view_vidhan_sabha',
                'view_blocks',
                'view_panchayats',
                'view_villages',
                'view_booths',
                'view_form_list',
                'view_respondent_table',
                'view_teams',
                'view_employee_analysis',
            ];
            $viewerPermissionIds = Permission::whereIn('name', $viewerPermissions)->pluck('id')->toArray();
            $viewerRole->permissions()->sync($viewerPermissionIds);
        }

        // Guest permissions (minimal)
        if ($guestRole) {
            $guestPermissions = [
                'view_dashboard',
                'view_employee_management',
                'view_parliament_management',
                'view_lok_sabha',
                'view_vidhan_sabha',
                'view_blocks',
                'view_panchayats',
                'view_villages',
                'view_booths',
            ];
            $guestPermissionIds = Permission::whereIn('name', $guestPermissions)->pluck('id')->toArray();
            $guestRole->permissions()->sync($guestPermissionIds);
        }
    }
}
