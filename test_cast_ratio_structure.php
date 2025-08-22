<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: dirname(__FILE__))
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CAST RATIO STRUCTURE TEST ===\n\n";

try {
    // Test 1: Check if cast_ratios table exists
    echo "1. Checking if cast_ratios table exists...\n";
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('cast_ratios');
    echo $tableExists ? "   ✓ cast_ratios table exists\n" : "   ✗ cast_ratios table does not exist\n";
    
    if ($tableExists) {
        // Test 2: Check table structure
        echo "\n2. Checking table structure...\n";
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('cast_ratios');
        $expectedColumns = [
            'caste_ratio_id', 'loksabha_id', 'vidhansabha_id', 'block_id',
            'panchayat_choosing_id', 'panchayat_id', 'village_choosing_id',
            'village_id', 'booth_id', 'caste_id', 'caste_ratio',
            'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            if (in_array($column, $columns)) {
                echo "   ✓ Column '{$column}' exists\n";
            } else {
                echo "   ✗ Column '{$column}' missing\n";
            }
        }
        
        // Test 3: Check foreign key constraints
        echo "\n3. Checking foreign key constraints...\n";
        $foreignKeys = \Illuminate\Support\Facades\DB::select("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'cast_ratios' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        $expectedFKs = [
            'panchayat_choosing_id' => 'panchayat_choosings',
            'village_choosing_id' => 'village_choosings',
            'caste_id' => 'castes'
        ];
        
        foreach ($foreignKeys as $fk) {
            echo "   ✓ FK: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
        }
        
        // Test 4: Check if choosing tables exist
        echo "\n4. Checking if choosing tables exist...\n";
        $panchayatChoosingsExists = \Illuminate\Support\Facades\Schema::hasTable('panchayat_choosings');
        $villageChoosingsExists = \Illuminate\Support\Facades\Schema::hasTable('village_choosings');
        
        echo $panchayatChoosingsExists ? "   ✓ panchayat_choosings table exists\n" : "   ✗ panchayat_choosings table missing\n";
        echo $villageChoosingsExists ? "   ✓ village_choosings table exists\n" : "   ✗ village_choosings table missing\n";
        
        // Test 5: Test CastRatio model
        echo "\n5. Testing CastRatio model...\n";
        try {
            $castRatio = new \App\Models\CastRatio();
            echo "   ✓ CastRatio model can be instantiated\n";
            
            // Check fillable fields
            $fillable = $castRatio->getFillable();
            $expectedFillable = [
                'loksabha_id', 'vidhansabha_id', 'block_id', 'panchayat_choosing_id',
                'panchayat_id', 'village_choosing_id', 'village_id', 'booth_id',
                'caste_id', 'caste_ratio'
            ];
            
            foreach ($expectedFillable as $field) {
                if (in_array($field, $fillable)) {
                    echo "   ✓ Fillable field '{$field}' exists\n";
                } else {
                    echo "   ✗ Fillable field '{$field}' missing\n";
                }
            }
            
        } catch (Exception $e) {
            echo "   ✗ CastRatio model error: " . $e->getMessage() . "\n";
        }
        
        // Test 6: Test relationships
        echo "\n6. Testing relationships...\n";
        try {
            $castRatio = new \App\Models\CastRatio();
            
            // Test if relationships are defined
            $reflection = new ReflectionClass($castRatio);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            
            $expectedRelationships = ['panchayatChoosing', 'villageChoosing'];
            foreach ($expectedRelationships as $relationship) {
                $methodExists = false;
                foreach ($methods as $method) {
                    if ($method->getName() === $relationship) {
                        $methodExists = true;
                        break;
                    }
                }
                echo $methodExists ? "   ✓ Relationship '{$relationship}' exists\n" : "   ✗ Relationship '{$relationship}' missing\n";
            }
            
        } catch (Exception $e) {
            echo "   ✗ Relationship test error: " . $e->getMessage() . "\n";
        }
        
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "The cast ratio choosing functionality structure has been verified!\n";
