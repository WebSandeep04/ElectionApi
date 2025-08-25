<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Seeded Caste Categories Demo ===\n\n";

try {
    // Get all categories with their castes
    echo "1. All Categories with Castes:\n";
    $categories = \App\Models\CasteCategory::with('castes')->get();
    
    foreach ($categories as $category) {
        echo "\n   📁 {$category->name} Category ({$category->castes->count()} castes)\n";
        echo "   Description: {$category->description}\n";
        
        if ($category->castes->count() > 0) {
            echo "   Castes:\n";
            foreach ($category->castes as $caste) {
                echo "     • {$caste->caste}\n";
            }
        } else {
            echo "   No castes assigned yet\n";
        }
    }
    
    // Test specific category API
    echo "\n2. Testing API for specific categories:\n";
    
    $testCategories = ['General', 'OBC', 'SC', 'ST', 'EWS'];
    foreach ($testCategories as $categoryName) {
        $category = \App\Models\CasteCategory::where('name', $categoryName)->first();
        if ($category) {
            echo "\n   🔍 {$categoryName} Category (ID: {$category->id}):\n";
            $castes = $category->castes;
            echo "   Found {$castes->count()} castes:\n";
            foreach ($castes as $caste) {
                echo "     • {$caste->caste} (ID: {$caste->id})\n";
            }
        }
    }
    
    // Show API endpoints
    echo "\n3. Available API Endpoints:\n";
    echo "   🌐 GET /api/caste-categories - List all categories with castes\n";
    echo "   🔍 GET /api/caste-categories/1 - Get General category with castes\n";
    echo "   🔍 GET /api/caste-categories/2 - Get OBC category with castes\n";
    echo "   🔍 GET /api/caste-categories/3 - Get SC category with castes\n";
    echo "   🔍 GET /api/caste-categories/4 - Get ST category with castes\n";
    echo "   🔍 GET /api/caste-categories/5 - Get EWS category with castes\n";
    echo "   📋 GET /api/caste-categories/{id}/castes - Get castes by category ID\n";
    
    // Show sample API responses
    echo "\n4. Sample API Response for /api/caste-categories:\n";
    $sampleResponse = [
        'data' => $categories->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'castes_count' => $category->castes->count(),
                'castes' => $category->castes->map(function($caste) {
                    return [
                        'id' => $caste->id,
                        'caste' => $caste->caste
                    ];
                })
            ];
        })
    ];
    
    echo json_encode($sampleResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    echo "\n\n5. Frontend Usage Example:\n";
    echo "   // Get all categories for dropdown\n";
    echo "   fetch('/api/caste-categories')\n";
    echo "     .then(response => response.json())\n";
    echo "     .then(data => {\n";
    echo "       data.data.forEach(category => {\n";
    echo "         console.log(\`\${category.name}: \${category.castes_count} castes\`);\n";
    echo "       });\n";
    echo "     });\n\n";
    
    echo "   // Get castes for selected category\n";
    echo "   function getCastesByCategory(categoryId) {\n";
    echo "     fetch(\`/api/caste-categories/\${categoryId}/castes\`)\n";
    echo "       .then(response => response.json())\n";
    echo "       .then(data => {\n";
    echo "         if (data.success) {\n";
    echo "           console.log('Castes:', data.data.castes);\n";
    echo "         }\n";
    echo "       });\n";
    echo "   }\n";
    
    echo "\n=== Demo completed! ===\n";
    echo "\n✅ Categories created: " . $categories->count() . "\n";
    echo "✅ Total castes: " . $categories->sum(function($c) { return $c->castes->count(); }) . "\n";
    echo "✅ API ready for use!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
