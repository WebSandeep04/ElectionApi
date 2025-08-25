<?php

namespace Database\Seeders;

use App\Models\Caste;
use App\Models\CasteCategory;
use Illuminate\Database\Seeder;

class CasteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $generalCategory = CasteCategory::where('name', 'General')->first();
        $obcCategory = CasteCategory::where('name', 'OBC')->first();
        $scCategory = CasteCategory::where('name', 'SC')->first();
        $stCategory = CasteCategory::where('name', 'ST')->first();
        $ewsCategory = CasteCategory::where('name', 'EWS')->first();

        $castes = [
            // General Category Castes
            [
                'caste' => 'Brahmin',
                'category_id' => $generalCategory ? $generalCategory->id : null
            ],
            [
                'caste' => 'Rajput',
                'category_id' => $generalCategory ? $generalCategory->id : null
            ],
            [
                'caste' => 'Bania',
                'category_id' => $generalCategory ? $generalCategory->id : null
            ],
            [
                'caste' => 'Kayastha',
                'category_id' => $generalCategory ? $generalCategory->id : null
            ],

            // OBC Category Castes
            [
                'caste' => 'Yadav',
                'category_id' => $obcCategory ? $obcCategory->id : null
            ],
            [
                'caste' => 'Kurmi',
                'category_id' => $obcCategory ? $obcCategory->id : null
            ],
            [
                'caste' => 'Lodhi',
                'category_id' => $obcCategory ? $obcCategory->id : null
            ],
            [
                'caste' => 'Gujjar',
                'category_id' => $obcCategory ? $obcCategory->id : null
            ],
            [
                'caste' => 'Jat',
                'category_id' => $obcCategory ? $obcCategory->id : null
            ],

            // SC Category Castes
            [
                'caste' => 'Chamar',
                'category_id' => $scCategory ? $scCategory->id : null
            ],
            [
                'caste' => 'Pasi',
                'category_id' => $scCategory ? $scCategory->id : null
            ],
            [
                'caste' => 'Dhobi',
                'category_id' => $scCategory ? $scCategory->id : null
            ],
            [
                'caste' => 'Balmiki',
                'category_id' => $scCategory ? $scCategory->id : null
            ],

            // ST Category Castes
            [
                'caste' => 'Gond',
                'category_id' => $stCategory ? $stCategory->id : null
            ],
            [
                'caste' => 'Bhil',
                'category_id' => $stCategory ? $stCategory->id : null
            ],
            [
                'caste' => 'Santhal',
                'category_id' => $stCategory ? $stCategory->id : null
            ],
            [
                'caste' => 'Munda',
                'category_id' => $stCategory ? $stCategory->id : null
            ],

            // EWS Category Castes (can be any caste that falls under EWS criteria)
            [
                'caste' => 'General EWS',
                'category_id' => $ewsCategory ? $ewsCategory->id : null
            ],
            [
                'caste' => 'OBC EWS',
                'category_id' => $ewsCategory ? $ewsCategory->id : null
            ]
        ];

        foreach ($castes as $casteData) {
            Caste::firstOrCreate(
                ['caste' => $casteData['caste']],
                $casteData
            );
        }

        $this->command->info('Castes seeded successfully!');
        
        // Show summary
        $categoryCounts = [];
        foreach (['General', 'OBC', 'SC', 'ST', 'EWS'] as $categoryName) {
            $category = CasteCategory::where('name', $categoryName)->first();
            if ($category) {
                $count = Caste::where('category_id', $category->id)->count();
                $categoryCounts[] = "$categoryName: $count castes";
            }
        }
        
        $this->command->info('Category summary: ' . implode(', ', $categoryCounts));
    }
}
