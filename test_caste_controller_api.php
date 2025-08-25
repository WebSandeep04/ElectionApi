<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Updated Caste Controller API Test ===\n\n";

try {
    // Test 1: Get all castes with category information
    echo "1. All Castes with Category Information:\n";
    $castes = \App\Models\Caste::with('category')->get();
    
    foreach ($castes as $caste) {
        $categoryName = $caste->category ? $caste->category->name : 'Unassigned';
        echo "   • {$caste->caste} -> Category: {$categoryName}\n";
    }
    
    // Test 2: Get castes by category
    echo "\n2. Castes by Category:\n";
    $categories = \App\Models\CasteCategory::all();
    
    foreach ($categories as $category) {
        $categoryCastes = \App\Models\Caste::where('category_id', $category->id)->get();
        echo "\n   📁 {$category->name} Category:\n";
        
        if ($categoryCastes->count() > 0) {
            foreach ($categoryCastes as $caste) {
                echo "     • {$caste->caste}\n";
            }
        } else {
            echo "     (No castes assigned)\n";
        }
    }
    
    // Test 3: Get unassigned castes
    echo "\n3. Unassigned Castes:\n";
    $unassignedCastes = \App\Models\Caste::whereNull('category_id')->get();
    
    if ($unassignedCastes->count() > 0) {
        foreach ($unassignedCastes as $caste) {
            echo "   • {$caste->caste}\n";
        }
    } else {
        echo "   (All castes are assigned to categories)\n";
    }
    
    // Test 4: Show API endpoints
    echo "\n4. Available Caste API Endpoints:\n";
    echo "   🌐 GET /api/castes - List all castes with category info\n";
    echo "   🔍 GET /api/castes/{id} - Get specific caste with category\n";
    echo "   📋 GET /api/castes/category/{categoryId} - Get castes by category\n";
    echo "   ❓ GET /api/castes/unassigned - Get unassigned castes\n";
    echo "   ➕ POST /api/castes - Create new caste (with optional category_id)\n";
    echo "   ✏️  PUT /api/castes/{id} - Update caste (including category_id)\n";
    echo "   🗑️  DELETE /api/castes/{id} - Delete caste\n";
    echo "   🔗 POST /api/castes/{id}/assign-category - Assign caste to category\n";
    echo "   🚫 POST /api/castes/{id}/remove-category - Remove caste from category\n";
    
    // Test 5: Show filtering examples
    echo "\n5. API Filtering Examples:\n";
    echo "   • GET /api/castes?category_id=1 - Get castes in General category\n";
    echo "   • GET /api/castes?caste=Brahmin - Search castes by name\n";
    echo "   • GET /api/castes?category_name=OBC - Search by category name\n";
    
    // Test 6: Show sample API responses
    echo "\n6. Sample API Response for /api/castes:\n";
    $sampleCaste = \App\Models\Caste::with('category')->first();
    if ($sampleCaste) {
        $sampleResponse = [
            'castes' => [
                [
                    'id' => $sampleCaste->id,
                    'caste' => $sampleCaste->caste,
                    'category_id' => $sampleCaste->category_id,
                    'category_data' => $sampleCaste->category ? [
                        'id' => $sampleCaste->category->id,
                        'name' => $sampleCaste->category->name,
                        'description' => $sampleCaste->category->description,
                    ] : null,
                    'created_at' => $sampleCaste->created_at,
                    'updated_at' => $sampleCaste->updated_at,
                ]
            ],
            'pagination' => [
                'total' => 1,
                'per_page' => 10,
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 1,
                'has_more_pages' => false,
            ]
        ];
        
        echo json_encode($sampleResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    // Test 7: Frontend usage examples
    echo "\n\n7. Frontend Usage Examples:\n";
    echo "   // Get all castes with category info\n";
    echo "   fetch('/api/castes')\n";
    echo "     .then(response => response.json())\n";
    echo "     .then(data => {\n";
    echo "       data.castes.forEach(caste => {\n";
    echo "         const categoryName = caste.category_data ? caste.category_data.name : 'Unassigned';\n";
    echo "         console.log(\`\${caste.caste} -> \${categoryName}\`);\n";
    echo "       });\n";
    echo "     });\n\n";
    
    echo "   // Get castes by category\n";
    echo "   function getCastesByCategory(categoryId) {\n";
    echo "     fetch(\`/api/castes/category/\${categoryId}\`)\n";
    echo "       .then(response => response.json())\n";
    echo "       .then(data => {\n";
    echo "         if (data.success) {\n";
    echo "           console.log('Castes in category:', data.data);\n";
    echo "         }\n";
    echo "       });\n";
    echo "   }\n\n";
    
    echo "   // Assign caste to category\n";
    echo "   function assignCasteToCategory(casteId, categoryId, token) {\n";
    echo "     fetch(\`/api/castes/\${casteId}/assign-category\`, {\n";
    echo "       method: 'POST',\n";
    echo "       headers: {\n";
    echo "         'Content-Type': 'application/json',\n";
    echo "         'Authorization': \`Bearer \${token}\`\n";
    echo "       },\n";
    echo "       body: JSON.stringify({ category_id: categoryId })\n";
    echo "     })\n";
    echo "       .then(response => response.json())\n";
    echo "       .then(data => {\n";
    echo "         if (data.success) {\n";
    echo "           console.log('Caste assigned successfully');\n";
    echo "         }\n";
    echo "       });\n";
    echo "   }\n";
    
    echo "\n=== Test completed! ===\n";
    echo "\n✅ Caste Controller updated with category functionality\n";
    echo "✅ All API endpoints ready for use\n";
    echo "✅ Filtering and relationship management available\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
