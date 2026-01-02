<?php

use App\Http\Controllers\API\V1\Health\HealthController;
use App\Http\Controllers\API\V1\POS\Sale\CreateSaleController;
use App\Http\Controllers\API\V1\POS\Sale\IndexSaleController;
use App\Http\Controllers\API\V1\POS\Sale\ShowSaleController;
use App\Http\Controllers\API\V1\POS\Menu\IndexMenuController;
use App\Http\Controllers\API\V1\POS\Menu\ShowMenuController;
use App\Http\Controllers\API\V1\POS\Combo\IndexComboController;
use App\Http\Controllers\API\V1\POS\Combo\ShowComboController;
use App\Http\Controllers\API\V1\POS\Category\IndexCategoryController;
use App\Http\Controllers\API\V1\POS\Category\ShowCategoryController;
use App\Http\Controllers\API\V1\POS\Customer\IndexCustomerController;
use App\Http\Controllers\API\V1\POS\Customer\ShowCustomerController;
use App\Http\Controllers\API\V1\Staff\Auth\StaffLoginController;
use App\Http\Controllers\API\V2\Staff\Auth\GetStaffAuthController;
use App\Http\Controllers\API\V2\Cashier\OpenShiftController;
use App\Http\Controllers\API\V2\Cashier\CloseShiftController;
use App\Http\Controllers\API\V2\Cashier\CurrentShiftController;
use App\Http\Controllers\API\V2\Cashier\DailySummaryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V2 Routes - Staff (Kasir) Only
|--------------------------------------------------------------------------
|
| Routes khusus untuk staff/kasir dengan akses terbatas:
| - Login dengan NIP
| - Buka/Tutup Shift
| - Lihat menu, kategori, combo (read-only)
| - Buat dan lihat transaksi penjualan
| - Lihat data customer
| - Ringkasan harian
|
| TIDAK ADA AKSES untuk:
| - Create/Update/Delete Menu
| - Create/Update/Delete Kategori
| - Create/Update/Delete Combo
| - Create/Update/Delete Customer
| - Manajemen User/Role/Permission
|
*/

// Health Check
Route::prefix('health')->group(function () {
    Route::get('/', [HealthController::class, 'check'])->name('v2.health.check');
    Route::get('/live', [HealthController::class, 'liveness'])->name('v2.health.liveness');
    Route::get('/ready', [HealthController::class, 'readiness'])->name('v2.health.readiness');
});

// Staff Login
Route::post(uri: 'login', action: StaffLoginController::class);

// Staff Authenticated Routes
Route::middleware(['auth:staff'])->group(function () {
    // Get authenticated staff profile
    Route::get(uri: 'auth', action: GetStaffAuthController::class);

    // Shift Management
    Route::prefix('shift')->group(function () {
        Route::get(uri: 'current', action: CurrentShiftController::class);
        Route::post(uri: 'open', action: OpenShiftController::class);
        Route::post(uri: 'close', action: CloseShiftController::class);
    });

    // Daily Summary
    Route::get(uri: 'summary/daily', action: DailySummaryController::class);

    // POS Operations for Staff
    Route::prefix('pos')->group(function () {
        // Sales - Staff can create and view sales
        Route::prefix('sales')->group(function () {
            Route::get(uri: '', action: IndexSaleController::class);
            Route::post(uri: '', action: CreateSaleController::class);
            Route::get(uri: '{sale}', action: ShowSaleController::class);
        });

        // Menu - Read Only
        Route::prefix('menu')->group(function () {
            Route::get(uri: '', action: IndexMenuController::class);
            Route::get(uri: '{menuItem}', action: ShowMenuController::class);
        });

        // Combos - Read Only
        Route::prefix('combos')->group(function () {
            Route::get(uri: '', action: IndexComboController::class);
            Route::get(uri: '{combo}', action: ShowComboController::class);
        });

        // Categories - Read Only
        Route::prefix('categories')->group(function () {
            Route::get(uri: '', action: IndexCategoryController::class);
            Route::get(uri: '{category}', action: ShowCategoryController::class);
        });

        // Customers - Read Only
        Route::prefix('customers')->group(function () {
            Route::get(uri: '', action: IndexCustomerController::class);
            Route::get(uri: '{customer}', action: ShowCustomerController::class);
        });
    });
});
