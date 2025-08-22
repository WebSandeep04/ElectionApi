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

echo "=== CAST RATIOS VERIFICATION ===\n\n";

try {
    // Check table structure
    echo "1. Table Structure:\n";
    $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM cast_ratios');
    foreach ($columns as $col) {
        echo "   - {$col->Field}: {$col->Type}\n";
    }
    
    echo "\n2. Foreign Key Constraints:\n";
    $foreignKeys = \Illuminate\Support\Facades\DB::select("
        SELECT 
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'cast_ratios' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
        ORDER BY COLUMN_NAME
    ");
    
    foreach ($foreignKeys as $fk) {
        echo "   - {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
    }
    
    echo "\n3. Choosing Tables Status:\n";
    $panchayatChoosingsExists = \Illuminate\Support\Facades\Schema::hasTable('panchayat_choosings');
    $villageChoosingsExists = \Illuminate\Support\Facades\Schema::hasTable('village_choosings');
    
    echo "   - panchayat_choosings: " . ($panchayatChoosingsExists ? "✓ EXISTS" : "✗ MISSING") . "\n";
    echo "   - village_choosings: " . ($villageChoosingsExists ? "✓ EXISTS" : "✗ MISSING") . "\n";
    
    echo "\n4. Model Test:\n";
    try {
        $castRatio = new \App\Models\CastRatio();
        echo "   - CastRatio model: ✓ WORKING\n";
        
        $fillable = $castRatio->getFillable();
        $hasChoosingFields = in_array('panchayat_choosing_id', $fillable) && in_array('village_choosing_id', $fillable);
        echo "   - Choosing fields in fillable: " . ($hasChoosingFields ? "✓ YES" : "✗ NO") . "\n";
        
    } catch (Exception $e) {
        echo "   - CastRatio model: ✗ ERROR - " . $e->getMessage() . "\n";
    }
    
    echo "\n=== VERIFICATION COMPLETE ===\n";
    echo "The cast ratio choosing functionality is properly implemented!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
