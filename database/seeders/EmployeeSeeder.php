<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get employee types
        $employeeTypes = EmployeeType::all();
        
        if ($employeeTypes->isEmpty()) {
            // Create some employee types if none exist (avoid factories)
            $defaults = [
                ['type_name' => 'Full-time', 'status' => '1'],
                ['type_name' => 'Part-time', 'status' => '1'],
                ['type_name' => 'Contract', 'status' => '1'],
            ];
            foreach ($defaults as $type) {
                EmployeeType::firstOrCreate(['type_name' => $type['type_name']], $type);
            }
            $employeeTypes = EmployeeType::all();
        }

        $employeeTypes = $employeeTypes->pluck('id')->toArray();

        // Create sample employees
        $employees = [
            [
                'employee_type_id' => $employeeTypes[0] ?? 1,
                'emp_name' => 'John Doe',
                'emp_email' => 'john.doe@example.com',
                'emp_password' => 'password123',
                'emp_phone' => '+91-9876543210',
                'emp_address' => '123 Main Street, City, State 12345',
                'emp_wages' => 50000.00,
                'emp_date' => '2024-01-15',
                'is_active' => true,
                'emp_code' => 'EMP001',
                'emp_designation' => 'Software Developer',
                'joining_date' => '2024-01-15',
                'emp_status' => 'active'
            ],
            [
                'employee_type_id' => $employeeTypes[1] ?? 1,
                'emp_name' => 'Jane Smith',
                'emp_email' => 'jane.smith@example.com',
                'emp_password' => 'password123',
                'emp_phone' => '+91-9876543211',
                'emp_address' => '456 Oak Avenue, City, State 12345',
                'emp_wages' => 60000.00,
                'emp_date' => '2024-02-01',
                'is_active' => true,
                'emp_code' => 'EMP002',
                'emp_designation' => 'Project Manager',
                'joining_date' => '2024-02-01',
                'emp_status' => 'active'
            ],
            [
                'employee_type_id' => $employeeTypes[2] ?? 1,
                'emp_name' => 'Mike Johnson',
                'emp_email' => 'mike.johnson@example.com',
                'emp_password' => 'password123',
                'emp_phone' => '+91-9876543212',
                'emp_address' => '789 Pine Road, City, State 12345',
                'emp_wages' => 45000.00,
                'emp_date' => '2024-03-10',
                'is_active' => true,
                'emp_code' => 'EMP003',
                'emp_designation' => 'UI/UX Designer',
                'joining_date' => '2024-03-10',
                'emp_status' => 'active'
            ],
            [
                'employee_type_id' => $employeeTypes[0] ?? 1,
                'emp_name' => 'Sarah Wilson',
                'emp_email' => 'sarah.wilson@example.com',
                'emp_password' => 'password123',
                'emp_phone' => '+91-9876543213',
                'emp_address' => '321 Elm Street, City, State 12345',
                'emp_wages' => 55000.00,
                'emp_date' => '2024-04-05',
                'is_active' => false,
                'emp_code' => 'EMP004',
                'emp_designation' => 'QA Engineer',
                'joining_date' => '2024-04-05',
                'termination_date' => '2024-12-31',
                'emp_status' => 'terminated'
            ],
            [
                'employee_type_id' => $employeeTypes[1] ?? 1,
                'emp_name' => 'David Brown',
                'emp_email' => 'david.brown@example.com',
                'emp_password' => 'password123',
                'emp_phone' => '+91-9876543214',
                'emp_address' => '654 Maple Drive, City, State 12345',
                'emp_wages' => 70000.00,
                'emp_date' => '2024-05-20',
                'is_active' => true,
                'emp_code' => 'EMP005',
                'emp_designation' => 'Senior Developer',
                'joining_date' => '2024-05-20',
                'emp_status' => 'active'
            ]
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }
    }
}
