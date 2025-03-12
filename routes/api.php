<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReservationController;


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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/leads', [LeadController::class, 'store']); // Create Lead
    Route::put('/leads/{lead}/assign', [LeadController::class, 'assign']); // Assign Lead
    Route::put('/leads/{lead}/progress', [LeadController::class, 'progress']); // Progress Lead
    Route::put('/leads/{lead}/cancel', [LeadController::class, 'cancel']); // Cancel Lead
    Route::get('/leads', [LeadController::class, 'index']); // Retrieve Leads
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store']); // Create Reservation
    Route::put('/reservations/{reservation}/approve-financials', [ReservationController::class, 'approveFinancials']); // Approve Financials
    Route::put('/reservations/{reservation}/finalize-legal', [ReservationController::class, 'finalizeLegal']); // Finalize Legal
    Route::get('/reservations', [ReservationController::class, 'index']); // Retrieve Reservations
});
