<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CasteController;
use App\Http\Controllers\LokSabhaController;
use App\Http\Controllers\VidhanSabhaController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\PanchayatController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\BoothController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDocumentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CastRatioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
// Public read for Employee Types
Route::apiResource('employee-types', EmployeeTypeController::class)->only(['index', 'show']);

// Public read for Employees
Route::apiResource('employees', EmployeeController::class)->only(['index', 'show']);
Route::get('employees/type/{employeeTypeId}', [EmployeeController::class, 'getByEmployeeType']);
Route::get('employees/active', [EmployeeController::class, 'getActive']);
Route::get('employees/inactive', [EmployeeController::class, 'getInactive']);

// Public read for Employee Documents
Route::apiResource('employee-documents', EmployeeDocumentController::class)->only(['index', 'show']);
Route::get('employee-documents/employee/{employeeId}', [EmployeeDocumentController::class, 'getByEmployee']);
Route::get('employee-documents/type/{documentType}', [EmployeeDocumentController::class, 'getByType']);
Route::get('employee-documents/{document}/download', [EmployeeDocumentController::class, 'download']);

// Public read for Roles
Route::get('roles/active', [RoleController::class, 'getActive']);
Route::get('roles/inactive', [RoleController::class, 'getInactive']);
Route::apiResource('roles', RoleController::class)->only(['index', 'show']);
Route::get('roles/{role}/permissions', [RoleController::class, 'permissions']);
Route::apiResource('permissions', PermissionController::class)->only(['index', 'show']);

// Public read for Users
Route::get('users/active', [UserController::class, 'getActive']);
Route::get('users/inactive', [UserController::class, 'getInactive']);
Route::get('users/role/{roleId}', [UserController::class, 'getByRole']);
Route::apiResource('users', UserController::class)->only(['index', 'show']);

// Public read for CastRatios
Route::apiResource('cast-ratios', CastRatioController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

// Caste CRUD
Route::apiResource('castes', CasteController::class)->only(['index', 'show']);

// Forms public read
Route::get('/forms', [FormController::class, 'index']);
Route::get('/forms/{id}', [FormController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // Forms protected writes
    Route::post('/forms', [FormController::class, 'store']);
    Route::put('/forms/{id}', [FormController::class, 'update']);
    Route::delete('/forms/{id}', [FormController::class, 'destroy']);

    // Employee Types protected writes
    Route::apiResource('employee-types', EmployeeTypeController::class)->only(['store', 'update', 'destroy']);

    // Employees protected writes
    Route::apiResource('employees', EmployeeController::class)->only(['store', 'update', 'destroy']);

    // Employee Documents protected writes
    Route::apiResource('employee-documents', EmployeeDocumentController::class)->only(['store', 'update', 'destroy']);
    Route::post('employee-documents/{document}/verify', [EmployeeDocumentController::class, 'verify']);
    Route::post('employee-documents/{document}/unverify', [EmployeeDocumentController::class, 'unverify']);

    // Roles protected writes (require manage_roles)
    Route::middleware('permission:manage_roles')->group(function () {
        Route::apiResource('roles', RoleController::class)->only(['store', 'update', 'destroy']);
        Route::post('roles/{role}/activate', [RoleController::class, 'activate']);
        Route::post('roles/{role}/deactivate', [RoleController::class, 'deactivate']);
        Route::post('roles/{role}/permissions', [RoleController::class, 'syncPermissions']);
    });

    // Permissions protected writes (require manage_permissions)
    Route::middleware('permission:manage_permissions')->group(function () {
        Route::apiResource('permissions', PermissionController::class)->only(['store', 'update', 'destroy']);
    });

    // Users protected writes
    Route::apiResource('users', UserController::class)->only(['store', 'update', 'destroy']);
    Route::post('users/{user}/activate', [UserController::class, 'activate']);
    Route::post('users/{user}/deactivate', [UserController::class, 'deactivate']);

    // CastRatios protected writes
    Route::apiResource('cast-ratios', CastRatioController::class)->only(['store', 'update', 'destroy'])
        ->middleware('permission:manage_cast_ratios');

    Route::apiResource('castes', CasteController::class)->only(['store', 'update', 'destroy']);
});

// Lok Sabha CRUD
Route::apiResource('lok-sabhas', LokSabhaController::class)->only(['index', 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('lok-sabhas', LokSabhaController::class)->only(['store', 'update', 'destroy']);
});

// Vidhan Sabha CRUD
Route::apiResource('vidhan-sabhas', VidhanSabhaController::class)->only(['index', 'show']);
Route::get('vidhan-sabhas/lok-sabha/{loksabhaId}', [VidhanSabhaController::class, 'getByLokSabha']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('vidhan-sabhas', VidhanSabhaController::class)->only(['store', 'update', 'destroy']);
});

// Block CRUD
Route::apiResource('blocks', BlockController::class)->only(['index', 'show']);
Route::get('blocks/lok-sabha/{loksabhaId}', [BlockController::class, 'getByLokSabha']);
Route::get('blocks/vidhan-sabha/{vidhansabhaId}', [BlockController::class, 'getByVidhanSabha']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('blocks', BlockController::class)->only(['store', 'update', 'destroy']);
});

// Panchayat CRUD
Route::apiResource('panchayats', PanchayatController::class)->only(['index', 'show']);
Route::get('panchayats/lok-sabha/{loksabhaId}', [PanchayatController::class, 'getByLokSabha']);
Route::get('panchayats/vidhan-sabha/{vidhansabhaId}', [PanchayatController::class, 'getByVidhanSabha']);
Route::get('panchayats/block/{blockId}', [PanchayatController::class, 'getByBlock']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('panchayats', PanchayatController::class)->only(['store', 'update', 'destroy']);
});

// Village CRUD
Route::apiResource('villages', VillageController::class)->only(['index', 'show']);
Route::get('villages/lok-sabha/{loksabhaId}', [VillageController::class, 'getByLokSabha']);
Route::get('villages/vidhan-sabha/{vidhansabhaId}', [VillageController::class, 'getByVidhanSabha']);
Route::get('villages/block/{blockId}', [VillageController::class, 'getByBlock']);
Route::get('villages/panchayat/{panchayatId}', [VillageController::class, 'getByPanchayat']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('villages', VillageController::class)->only(['store', 'update', 'destroy']);
});

// Booth CRUD
Route::apiResource('booths', BoothController::class)->only(['index', 'show']);
Route::get('booths/lok-sabha/{loksabhaId}', [BoothController::class, 'getByLokSabha']);
Route::get('booths/vidhan-sabha/{vidhansabhaId}', [BoothController::class, 'getByVidhanSabha']);
Route::get('booths/block/{blockId}', [BoothController::class, 'getByBlock']);
Route::get('booths/panchayat/{panchayatId}', [BoothController::class, 'getByPanchayat']);
Route::get('booths/village/{villageId}', [BoothController::class, 'getByVillage']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('booths', BoothController::class)->only(['store', 'update', 'destroy']);
});
