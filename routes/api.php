<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ApplicationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('applications', ApplicationController::class, ['except' => ['destroy']]);
    Route::get('/users/{email}/{application}',[UserController::class, 'show']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::resource('users', UserController::class, ['only' => ['index', 'store']]);
    Route::post('/logout', [AuthController::class, 'logout']);
});
