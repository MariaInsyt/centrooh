<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AgentDistrictController;
use App\Http\Controllers\BillboardController;
use App\Http\Controllers\OneTimePasswordController;
use App\Http\Controllers\DeviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::controller(AgentController::class)
    ->middleware('auth:sanctum')
    ->group(
    function () {
        Route::get('/agent', 'agent');
    }
);

Route::controller(BillboardController::class)
    ->middleware('auth:sanctum')
    ->group(
    function () {
        Route::get('/billboard/{billboardId}', 'billboard');
        Route::get('/billboards/agent', 'agentBillboards');
    }
);

Route::controller(DeviceController::class)
    ->middleware('auth:sanctum')
    ->group(
    function () {
        Route::post('/ping', 'ping');
    }
);

Route::controller(AgentDistrictController::class)
    ->middleware('auth:sanctum')
    ->group(
    function () {
        Route::get('/agent/districts', 'agentDistricts');
    }
);

Route::controller(OneTimePasswordController::class)->group(
    function () {
        Route::post('/otp', 'sendOtp');
        Route::post('/otp/verify', 'verifyOtp');
    }
);

Route::post('/agent/register', [AgentController::class, 'create']);
