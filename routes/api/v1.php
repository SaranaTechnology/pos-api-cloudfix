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
});

Route::get(uri: 'subriptions', action:IndexSubsriptionController::class)->middleware(['auth:api']);
Route::any(uri:'callback',action:CallbackController::class)->name('callback');
