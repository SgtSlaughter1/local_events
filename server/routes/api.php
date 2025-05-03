<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReviewController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizerController;
use App\Http\Middleware\OrganizerMiddleware;

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

// Health check route
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!'
    ]);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Event routes (public)
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/events/{event}/reviews', [ReviewController::class, 'index']);

// Category routes (public)
Route::get('/categories', function () {
    return response()->json(['categories' => Category::active()->get()]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Event management
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
    Route::post('/events/{event}/register', [EventController::class, 'register']);
    Route::get('/my-events', [EventController::class, 'myEvents']);

    // Reviews
    Route::post('/events/{event}/reviews', [ReviewController::class, 'store']);
    Route::delete('/events/{event}/reviews/{review}', [ReviewController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', OrganizerMiddleware::class])->prefix('organizer')->group(function () {
    Route::get('events', [OrganizerController::class, 'index']);
    Route::post('events', [OrganizerController::class, 'store']);
    Route::get('events/{id}', [OrganizerController::class, 'show']);
    Route::put('events/{id}', [OrganizerController::class, 'update']);
    Route::delete('events/{id}', [OrganizerController::class, 'destroy']);
    Route::get('events/{id}/registrations', [OrganizerController::class, 'registrations']);
    Route::put('registrations/{registration_id}', [OrganizerController::class, 'updateRegistration']);
    Route::get('events/{id}/stats', [OrganizerController::class, 'stats']);
});
