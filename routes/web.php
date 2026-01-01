<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Docs\OpenApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', [OpenApiController::class, 'ui']);
Route::get('/docs/openapi.{extension}', [OpenApiController::class, 'spec'])
    ->where('extension', 'yaml|yml');
ad
