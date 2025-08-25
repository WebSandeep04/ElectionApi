<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Caste Category API Demo ===\n\n";

try {
    // Step 1: Create some test categories
    echo "1. Creating test categories...\n";
    
    $category1 = \App\Models\CasteCategory::create([
        'name' => 'General Category',
        'description' => 'General category for castes'
    ]);
    echo "   Created category: {$category1->name} (ID: {$category1->id})\n";
    
    $category2 = \App\Models\CasteCategory::create([
        'name' => 'OBC Category',
        'description' => 'Other Backward Classes category'
    ]);
    echo "   Created category: {$category2->name} (ID: {$category2->id})\n";
    
    $category3 = \App\Models\CasteCategory::create([
        'name' => 'SC Category',
        'description' => 'Scheduled Caste category'
    ]);
    echo "   Created category: {$category3->name} (ID: {$category3->id})\n";
    
    // Step 2: Create some test castes and assign them to categories
    echo "\n2. Creating test castes...\n";
    
    $caste1 = \App\Models\Caste::create([
        'caste' => 'General',
        'category_id' => $category1->id
    ]);
    echo "   Created caste: {$caste1->caste} -> Category: {$category1->name}\n";
    
    $caste2 = \App\Models\Caste::create([
        'caste' => 'Brahmin',
        'category_id' => $category1->id
    ]);
    echo "   Created caste: {$caste2->caste} -> Category: {$category1->name}\n";
    
    $caste3 = \App\Models\Caste::create([
        'caste' => 'Yadav',
        'category_id' => $category2->id
    ]);
    echo "   Created caste: {$caste3->caste} -> Category: {$category2->name}\n";
    
    $caste4 = \App\Models\Caste::create([
        'caste' => 'Kurmi',
        'category_id' => $category2->id
    ]);
    echo "   Created caste: {$caste4->caste} -> Category: {$category2->name}\n";
    
    $caste5 = \App\Models\Caste::create([
        'caste' => 'Chamar',
        'category_id' => $category3->id
    ]);
    echo "   Created caste: {$caste5->caste} -> Category: {$category3->name}\n";
    
    // Step 3: Demonstrate the API functionality
    echo "\n3. Testing API functionality...\n";
    
    // Get all categories with their castes
    echo "\n   All Categories with Castes:\n";
    $categories = \App\Models\CasteCategory::with('castes')->get();
    foreach ($categories as $category) {
        echo "   - {$category->name}: " . $category->castes->count() . " castes\n";
        foreach ($category->castes as $caste) {
            echo "     * {$caste->caste}\n";
        }
    }
    
    // Get castes by specific category
    echo "\n   Castes in '{$category2->name}' category:\n";
    $obcCastes = $category2->castes;
    foreach ($obcCastes as $caste) {
        echo "   - {$caste->caste}\n";
    }
    
    // Get category from caste
    echo "\n   Category for '{$caste1->caste}' caste:\n";
    $casteWithCategory = \App\Models\Caste::with('category')->find($caste1->id);
    echo "   - {$casteWithCategory->caste} belongs to: {$casteWithCategory->category->name}\n";
    
    // Step 4: Show API endpoints
    echo "\n4. Available API Endpoints:\n";
    echo "   GET /api/caste-categories - List all categories\n";
    echo "   GET /api/caste-categories/{id} - Get specific category with castes\n";
    echo "   GET /api/caste-categories/{id}/castes - Get castes by category ID\n";
    echo "   POST /api/caste-categories - Create new category (requires auth)\n";
    echo "   PUT /api/caste-categories/{id} - Update category (requires auth)\n";
    echo "   DELETE /api/caste-categories/{id} - Delete category (requires auth)\n";
    
    // Step 5: Clean up test data
    echo "\n5. Cleaning up test data...\n";
    
    // Delete castes first (due to foreign key constraint)
    $caste1->delete();
    $caste2->delete();
    $caste3->delete();
    $caste4->delete();
    $caste5->delete();
    echo "   Deleted all test castes\n";
    
    // Delete categories
    $category1->delete();
    $category2->delete();
    $category3->delete();
    echo "   Deleted all test categories\n";
    
    echo "\n=== Demo completed successfully! ===\n";
    echo "\nTo use the API:\n";
    echo "1. Create a category: POST /api/caste-categories\n";
    echo "2. Create castes with category_id: POST /api/castes\n";
    echo "3. Get castes by category: GET /api/caste-categories/{categoryId}/castes\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
