<?php

namespace Database\Seeders;

use App\Models\CasteCategory;
use Illuminate\Database\Seeder;

class CasteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General',
                'description' => 'General category for unreserved castes'
            ],
            [
                'name' => 'OBC',
                'description' => 'Other Backward Classes category'
            ],
            [
                'name' => 'SC',
                'description' => 'Scheduled Caste category'
            ],
            [
                'name' => 'ST',
                'description' => 'Scheduled Tribe category'
            ],
            [
                'name' => 'EWS',
                'description' => 'Economically Weaker Section category'
            ]
        ];

        foreach ($categories as $categoryData) {
            CasteCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        $this->command->info('Caste categories seeded successfully!');
        $this->command->info('Created categories: ' . implode(', ', array_column($categories, 'name')));
    }
}
