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

//  Public Authentication Routes (Register, Login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//  Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/leads/{lead_id}/status-logs', [LeadStatusLogController::class, 'index']);

    //  Sales Agent Routes (Manage Leads & Create Reservations)
    Route::middleware('role:sales_agent')->group(function () {
        Route::post('/leads', [LeadController::class, 'store']); // Create Lead
        Route::put('/leads/{lead}/progress', [LeadController::class, 'progress']); // Progress Lead
        Route::put('/leads/{lead}/cancel', [LeadController::class, 'cancel']); // Cancel Lead
        Route::get('/leads', [LeadController::class, 'index']); // Retrieve Leads
        Route::get('/leads/{id}', [LeadController::class, 'show']); // Get Lead by ID
        Route::get('/leads/{lead_id}/status-logs', [LeadStatusLogController::class, 'index']); // Lead Status Logs

        // Reservation Management (Agents Create Reservations)
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations', [ReservationController::class, 'index']); // Retrieve Reservations
    });

    //  Admin Routes (Lead Assignment, Approvals, Property Management)
    Route::middleware('role:admin')->group(function () {
        // Lead Assignment (Only Admins Can Assign Leads)
        Route::put('/leads/{lead}/assign', [LeadController::class, 'assign']);

        // Reservation Approval (Admins Only)
        Route::put('/reservations/{reservation}/approve-financials', [ReservationController::class, 'approveFinancials']);
        Route::put('/reservations/{reservation}/finalize-legal', [ReservationController::class, 'finalizeLegal']);

        // Property Management (Admins Only)
        Route::post('/properties', [PropertyController::class, 'store']);
        Route::put('/properties/{property}', [PropertyController::class, 'update']);
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);
    });
});
