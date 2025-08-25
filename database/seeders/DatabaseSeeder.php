<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\FormBuilderSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\CasteCategorySeeder;
use Database\Seeders\CasteSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed core data first
		$this->call([
			RoleSeeder::class,
			FormBuilderSeeder::class,
			EmployeeSeeder::class,
			CasteCategorySeeder::class, // Seed categories first
			CasteSeeder::class, // Then seed castes with category relationships
		]);

        // Ensure the test user exists (after roles seeded so role_id resolves)
        \App\Models\User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role_id' => \App\Models\Role::where('name', 'admin')->first()?->id ?? 1,
            ]
        );
    }
}
