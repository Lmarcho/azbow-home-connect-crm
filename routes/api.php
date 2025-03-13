<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadStatusLogController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AuthController;

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

// Authentication Routes (Register, Login, Logout)
Route::post('/register', [AuthController::class, 'register']); // User Registration
Route::post('/login', [AuthController::class, 'login']); // User Login


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']); // User Logout

    //Lead Management APIs
    Route::post('/leads', [LeadController::class, 'store']); // Create Lead
    Route::put('/leads/{lead}/assign', [LeadController::class, 'assign']); // Assign Lead
    Route::put('/leads/{lead}/progress', [LeadController::class, 'progress']); // Progress Lead
    Route::put('/leads/{lead}/cancel', [LeadController::class, 'cancel']); // Cancel Lead
    Route::get('/leads', [LeadController::class, 'index']); // Retrieve Leads
    Route::get('/leads/{id}', [LeadController::class, 'show']); // Get Lead by ID
    Route::get('/leads/{lead_id}/status-logs', [LeadStatusLogController::class, 'index']);


    //Reservation Management APIs
    Route::post('/reservations', [ReservationController::class, 'store']); // Create Reservation
    Route::put('/reservations/{reservation}/approve-financials', [ReservationController::class, 'approveFinancials']); // Approve Financials
    Route::put('/reservations/{reservation}/finalize-legal', [ReservationController::class, 'finalizeLegal']); // Finalize Legal
    Route::get('/reservations', [ReservationController::class, 'index']); // Retrieve Reservations

    //Property Management APIs
    Route::post('/properties', [PropertyController::class, 'store']); // Create Property
    Route::get('/properties', [PropertyController::class, 'index']); // Retrieve Properties
    Route::put('/properties/{property}', [PropertyController::class, 'update']); // Update Property
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy']); // Delete Property
});
