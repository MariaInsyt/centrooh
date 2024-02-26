<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\BillboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OneTimePasswordController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(OneTimePasswordController::class)->group(
    function () {
        Route::post('/otp', 'sendOtp');
        Route::post('/otp/verify', 'verifyOtp');
    }
);

Route::post('/agent/register', [AgentController::class, 'create']);

Route::controller(AgentController::class)
    ->middleware('auth:sanctum')
    ->group(
    function () {
        Route::get('/agent', 'agent');
        //Agent's billboards
        Route::get('/agent/districts', 'agentDistricts');
    }
);

Route::controller(BillboardController::class)
    ->middleware('auth:sanctum')
    ->group(
    function () {
        Route::get('/billboard', 'billboard');
    }
);
