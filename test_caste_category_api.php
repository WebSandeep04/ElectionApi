<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CasteCategory API Test (Updated Structure) ===\n\n";

try {
    // Test 1: Check if caste_categories table exists
    echo "1. Checking caste_categories table...\n";
    $tableExists = DB::getSchemaBuilder()->hasTable('caste_categories');
    echo "   Table exists: " . ($tableExists ? 'YES' : 'NO') . "\n";
    
    if ($tableExists) {
        $columns = DB::getSchemaBuilder()->getColumnListing('caste_categories');
        echo "   Columns: " . implode(', ', $columns) . "\n";
        
        // Check if table has data
        $categoryCount = DB::table('caste_categories')->count();
        echo "   Records count: {$categoryCount}\n";
    }
    
    // Test 2: Check if castes table has category_id
    echo "\n2. Checking castes table for category_id...\n";
    $casteColumns = DB::getSchemaBuilder()->getColumnListing('castes');
    echo "   Caste table columns: " . implode(', ', $casteColumns) . "\n";
    
    if (in_array('category_id', $casteColumns)) {
        echo "   category_id column: EXISTS\n";
        
        // Check foreign key constraints
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = 'elect' 
            AND TABLE_NAME = 'castes' 
            AND COLUMN_NAME = 'category_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        echo "   Foreign Keys:\n";
        foreach ($foreignKeys as $fk) {
            echo "     {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
        }
    } else {
        echo "   category_id column: NOT FOUND\n";
    }
    
    // Test 3: Test model relationships
    echo "\n3. Testing model relationships...\n";
    
    // Test CasteCategory model
    $casteCategory = new \App\Models\CasteCategory();
    echo "   CasteCategory model created: " . ($casteCategory ? 'YES' : 'NO') . "\n";
    echo "   Fillable fields: " . implode(', ', $casteCategory->getFillable()) . "\n";
    
    // Test Caste model
    $caste = new \App\Models\Caste();
    echo "   Caste model created: " . ($caste ? 'YES' : 'NO') . "\n";
    echo "   Fillable fields: " . implode(', ', $caste->getFillable()) . "\n";
    
    // Test 4: Check API routes
    echo "\n4. Checking API routes...\n";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $casteCategoryRoutes = [];
    
    foreach ($routes as $route) {
        if (strpos($route->uri, 'caste-categories') !== false) {
            $casteCategoryRoutes[] = $route->methods[0] . ' ' . $route->uri;
        }
    }
    
    echo "   CasteCategory routes found: " . count($casteCategoryRoutes) . "\n";
    foreach ($casteCategoryRoutes as $route) {
        echo "     {$route}\n";
    }
    
    // Test 5: Check database structure
    echo "\n5. Checking database structure...\n";
    
    // Check if we can create a test category
    try {
        $testCategory = \App\Models\CasteCategory::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);
        echo "   Test category created: ID = {$testCategory->id}\n";
        
        // Check if we can create a test caste with category
        try {
            $testCaste = \App\Models\Caste::create([
                'caste' => 'Test Caste',
                'category_id' => $testCategory->id
            ]);
            echo "   Test caste created: ID = {$testCaste->id}, Category ID = {$testCaste->category_id}\n";
            
            // Test relationship
            $loadedCaste = \App\Models\Caste::with('category')->find($testCaste->id);
            echo "   Caste->Category relationship: " . ($loadedCaste->category ? 'WORKS' : 'FAILS') . "\n";
            
            $loadedCategory = \App\Models\CasteCategory::with('castes')->find($testCategory->id);
            echo "   Category->Castes relationship: " . ($loadedCategory->castes->count() . ' castes') . "\n";
            
            // Clean up test data
            $testCaste->delete();
            echo "   Test caste deleted\n";
            
        } catch (Exception $e) {
            echo "   Error creating test caste: " . $e->getMessage() . "\n";
        }
        
        $testCategory->delete();
        echo "   Test category deleted\n";
        
    } catch (Exception $e) {
        echo "   Error creating test category: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Test completed successfully! ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
