<?php

use App\Http\Controllers\API\V1\Permission\Apps\IndexAppsListController;
use App\Http\Controllers\API\V1\Permission\CRUD\IndexPermissionController;
use App\Http\Controllers\API\V1\Roles\CRUD\CreateRoleController;
use App\Http\Controllers\API\V1\Roles\CRUD\DeleteRoleController;
use App\Http\Controllers\API\V1\Roles\CRUD\DetailRoleController;
use App\Http\Controllers\API\V1\Roles\CRUD\IndexRoleController;
use App\Http\Controllers\API\V1\Roles\CRUD\UpdateRoleController;
use App\Http\Controllers\API\V1\Setting\Pay\CallbackController;
use App\Http\Controllers\API\V1\Setting\Pay\CreatePaymentUrlController;
use App\Http\Controllers\API\V1\Setting\Plan\IndexPlanController;
use App\Http\Controllers\API\V1\Setting\Subsribed\IndexSubsriptionController;
use App\Http\Controllers\API\V1\Shared\UploadPhotoController;
use App\Http\Controllers\API\V1\Sso\IndexSsoController;
use App\Http\Controllers\API\V1\User\Auth\EditProfileController;
use App\Http\Controllers\API\V1\User\Auth\GetDataAuthController;
use App\Http\Controllers\API\V1\User\Auth\LoginController;
use App\Http\Controllers\API\V1\User\CRUD\CreateUserController;
use App\Http\Controllers\API\V1\User\CRUD\DeleteUserController;
use App\Http\Controllers\API\V1\User\CRUD\DetailUserController;
use App\Http\Controllers\API\V1\User\CRUD\IndexUserController;
use App\Http\Controllers\API\V1\User\CRUD\UpdateUserController;
use App\Http\Controllers\API\V1\POS\Combo\CreateComboController;
use App\Http\Controllers\API\V1\POS\Combo\DeleteComboController;
use App\Http\Controllers\API\V1\POS\Combo\IndexComboController;
use App\Http\Controllers\API\V1\POS\Combo\ShowComboController;
use App\Http\Controllers\API\V1\POS\Combo\UpdateComboController;
use App\Http\Controllers\API\V1\POS\Customer\AdjustCustomerPointsController;
use App\Http\Controllers\API\V1\POS\Customer\CreateCustomerController;
use App\Http\Controllers\API\V1\POS\Customer\DeleteCustomerController;
use App\Http\Controllers\API\V1\POS\Customer\IndexCustomerController;
use App\Http\Controllers\API\V1\POS\Customer\ListCustomerTransactionsController;
use App\Http\Controllers\API\V1\POS\Customer\ShowCustomerController;
use App\Http\Controllers\API\V1\POS\Customer\UpdateCustomerController;
use App\Http\Controllers\API\V1\POS\Menu\CreateMenuController;
use App\Http\Controllers\API\V1\POS\Menu\DeleteMenuController;
use App\Http\Controllers\API\V1\POS\Menu\IndexMenuController;
use App\Http\Controllers\API\V1\POS\Menu\ShowMenuController;
use App\Http\Controllers\API\V1\POS\Menu\UpdateMenuController;
use App\Http\Controllers\API\V1\POS\Sale\CreateSaleController;
use App\Http\Controllers\API\V1\POS\Sale\PostCogsController;
use Illuminate\Support\Facades\Route;

Route::post(uri: 'login', action: LoginController::class);

Route::middleware(['auth:api'])->group(function () {
    Route::get(uri: 'auth', action: GetDataAuthController::class);
    Route::put(uri: 'auth', action: EditProfileController::class);
    Route::patch(uri: 'auth', action: EditProfileController::class);
});

Route::middleware(['auth:api'])->group(function () {
    Route::post(uri: 'upload', action: UploadPhotoController::class);
});

Route::prefix('user')->middleware(['auth:api'])->group(function () {
    Route::get(uri: '', action: IndexUserController::class);
    Route::post(uri: '', action: CreateUserController::class);
    Route::prefix('{user}')->group(function () {
        Route::get(uri: '', action: DetailUserController::class);
        Route::put(uri: '', action: UpdateUserController::class);
        Route::patch(uri: '', action: UpdateUserController::class);
        Route::delete(uri: '', action: DeleteUserController::class);
    });
});
Route::prefix('plan')->middleware(['auth:api'])->group(function () {
    Route::get(uri: '', action:IndexPlanController::class);
});
Route::prefix('payment')->middleware(['auth:api'])->group(function () {
    Route::post(uri: '{plan_id}', action: CreatePaymentUrlController::class);
});

Route::prefix('sso')->middleware(['auth:api'])->group(function () {
    Route::get(uri: '', action: IndexSsoController::class);
});

Route::prefix('roles')->middleware(['auth:api'])->group(function () {
    Route::get(uri: '', action: IndexRoleController::class);
    Route::post(uri: '', action: CreateRoleController::class);
    Route::prefix('{role}')->group(function () {
        Route::get(uri: '', action: DetailRoleController::class);
        Route::put(uri: '', action: UpdateRoleController::class);
        Route::delete(uri: '', action: DeleteRoleController::class);
    });
});

Route::prefix('permission')->middleware(['auth:api'])->group(function () {
    Route::get(uri: '', action: IndexPermissionController::class);
    Route::get(uri: '/apps', action: IndexAppsListController::class);
});

// POS
Route::prefix('pos')->middleware(['auth:api'])->group(function () {
    Route::post(uri: 'sales', action: CreateSaleController::class);
    Route::post(uri: 'sales/{sale}/post-cogs', action: PostCogsController::class);

    Route::prefix('menu')->group(function () {
        Route::get(uri: '', action: IndexMenuController::class);
        Route::post(uri: '', action: CreateMenuController::class);
        Route::get(uri: '{menuItem}', action: ShowMenuController::class);
        Route::put(uri: '{menuItem}', action: UpdateMenuController::class);
        Route::patch(uri: '{menuItem}', action: UpdateMenuController::class);
        Route::delete(uri: '{menuItem}', action: DeleteMenuController::class);
    });

    Route::prefix('combos')->group(function () {
        Route::get(uri: '', action: IndexComboController::class);
        Route::post(uri: '', action: CreateComboController::class);
        Route::get(uri: '{combo}', action: ShowComboController::class);
        Route::put(uri: '{combo}', action: UpdateComboController::class);
        Route::patch(uri: '{combo}', action: UpdateComboController::class);
        Route::delete(uri: '{combo}', action: DeleteComboController::class);
    });

    Route::prefix('customers')->group(function () {
        Route::get(uri: '', action: IndexCustomerController::class);
        Route::post(uri: '', action: CreateCustomerController::class);
        Route::get(uri: '{customer}', action: ShowCustomerController::class);
        Route::put(uri: '{customer}', action: UpdateCustomerController::class);
        Route::patch(uri: '{customer}', action: UpdateCustomerController::class);
        Route::delete(uri: '{customer}', action: DeleteCustomerController::class);
        Route::get(uri: '{customer}/loyalty', action: ListCustomerTransactionsController::class);
        Route::post(uri: '{customer}/loyalty', action: AdjustCustomerPointsController::class);
    });
});

Route::get(uri: 'subriptions', action:IndexSubsriptionController::class)->middleware(['auth:api']);
Route::any(uri:'callback',action:CallbackController::class)->name('callback');
