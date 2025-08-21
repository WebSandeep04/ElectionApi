<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
            
            // Master Data (Setup & Configuration)
            ['name' => 'view_employee_management', 'display_name' => 'View Employee Management'],
            ['name' => 'manage_employees', 'display_name' => 'Manage Employees'],
            ['name' => 'view_caste_management', 'display_name' => 'View Caste Management'],
            ['name' => 'manage_castes', 'display_name' => 'Manage Castes'],
            ['name' => 'view_caste_ratio', 'display_name' => 'View Caste Ratio'],
            ['name' => 'manage_caste_ratio', 'display_name' => 'Manage Caste Ratio'],
            ['name' => 'manage_cast_ratios', 'display_name' => 'Manage Cast Ratios'],
            ['name' => 'view_village_description', 'display_name' => 'View Village Description'],
            ['name' => 'manage_village_descriptions', 'display_name' => 'Manage Village Descriptions'],
            ['name' => 'view_education_management', 'display_name' => 'View Education Management'],
            ['name' => 'manage_educations', 'display_name' => 'Manage Educations'],
            ['name' => 'view_expense_category_management', 'display_name' => 'View Expense Category Management'],
            ['name' => 'manage_expense_categories', 'display_name' => 'Manage Expense Categories'],
            ['name' => 'view_category_management', 'display_name' => 'View Category Management'],
            ['name' => 'manage_categories', 'display_name' => 'Manage Categories'],
            ['name' => 'view_employee_types', 'display_name' => 'View Employee Types'],
            ['name' => 'manage_employee_types', 'display_name' => 'Manage Employee Types'],
            
            // User Management (Users & Roles Management)
            ['name' => 'view_user_management', 'display_name' => 'View User Management'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users'],
            ['name' => 'view_role_management', 'display_name' => 'View Role Management'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles'],
            ['name' => 'view_permission_management', 'display_name' => 'View Permission Management'],
            ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions'],
            
            // Parliament (Parliamentary Management)
            ['name' => 'view_parliament_management', 'display_name' => 'View Parliament Management'],
            ['name' => 'manage_parliament', 'display_name' => 'Manage Parliament'],
            ['name' => 'view_lok_sabha', 'display_name' => 'View Lok Sabha'],
            ['name' => 'manage_lok_sabha', 'display_name' => 'Manage Lok Sabha'],
            ['name' => 'view_vidhan_sabha', 'display_name' => 'View Vidhan Sabha'],
            ['name' => 'manage_vidhan_sabha', 'display_name' => 'Manage Vidhan Sabha'],
            ['name' => 'view_blocks', 'display_name' => 'View Blocks'],
            ['name' => 'manage_blocks', 'display_name' => 'Manage Blocks'],
            ['name' => 'view_panchayats', 'display_name' => 'View Panchayats'],
            ['name' => 'manage_panchayats', 'display_name' => 'Manage Panchayats'],
            ['name' => 'view_villages', 'display_name' => 'View Villages'],
            ['name' => 'manage_villages', 'display_name' => 'Manage Villages'],
            ['name' => 'view_booths', 'display_name' => 'View Booths'],
            ['name' => 'manage_booths', 'display_name' => 'Manage Booths'],
            
            // Data Collection & Forms (Forms & Information Gathering)
            ['name' => 'view_form_builder', 'display_name' => 'View Form Builder'],
            ['name' => 'manage_form_builder', 'display_name' => 'Manage Form Builder'],
            ['name' => 'view_form_list', 'display_name' => 'View Form List'],
            ['name' => 'manage_forms', 'display_name' => 'Manage Forms'],
            ['name' => 'view_respondent_table', 'display_name' => 'View Respondent Table'],
            ['name' => 'manage_respondent_table', 'display_name' => 'Manage Respondent Table'],
            ['name' => 'view_teams', 'display_name' => 'View Teams'],
            ['name' => 'manage_teams', 'display_name' => 'Manage Teams'],
            
            // Analysis & Tools (Reports & Performance Tracking)
            ['name' => 'view_employee_analysis', 'display_name' => 'View Employee Analysis'],
            ['name' => 'manage_employee_analysis', 'display_name' => 'Manage Employee Analysis'],
            ['name' => 'view_analysis', 'display_name' => 'View Analysis'],
            ['name' => 'manage_analysis', 'display_name' => 'Manage Analysis'],
            ['name' => 'view_cache_clear', 'display_name' => 'View Cache Clear'],
            ['name' => 'manage_cache_clear', 'display_name' => 'Manage Cache Clear'],
        ];

        foreach ($permissions as $permData) {
            Permission::firstOrCreate(
                ['name' => $permData['name']],
                array_merge($permData, [
                    'description' => 'Permission to ' . strtolower(str_replace('_', ' ', $permData['name'])),
                    'is_active' => true
                ])
            );
        }
    }
}
