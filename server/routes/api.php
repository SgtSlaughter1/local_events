<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReviewController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizerController;
use App\Http\Middleware\OrganizerMiddleware;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\Api\AttendeeController;



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
Route::post('/events/upload-image', [EventController::class, 'uploadImage']);

// Category routes (public)
Route::get('/categories', function () {
    return response()->json(['categories' => Category::active()->get()]);
});

Route::get('/user-types', [AuthController::class, 'userTypes']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Event routes
    Route::prefix('events')->group(function () {
        Route::post('/', [EventController::class, 'store']);
        Route::get('/', [EventController::class, 'index']);
        Route::get('/{event}', [EventController::class, 'show']);
        Route::put('/{event}', [EventController::class, 'update']);
        Route::delete('/{event}', [EventController::class, 'destroy']);
    });

    // Event management
    Route::post('/events/{event}/register', [EventController::class, 'register']);
    Route::get('/my-events', [EventController::class, 'myEvents']);

    // Reviews
    Route::post('/events/{event}/reviews', [ReviewController::class, 'store']);
    Route::delete('/events/{event}/reviews/{review}', [ReviewController::class, 'destroy']);

    // Organizer specific routes
    Route::middleware([OrganizerMiddleware::class])->prefix('organizer')->group(function () {
        Route::get('events', [EventController::class, 'organizerEvents']);
        Route::get('events/{event}/registrations', [EventController::class, 'eventRegistrations']);
        Route::put('registrations/{registration}', [EventController::class, 'updateRegistration']);
        Route::get('events/{event}/stats', [EventController::class, 'eventStats']);
    });

    // Attendee Routes
    Route::middleware(['auth:sanctum', 'role:attendee'])->group(function () {
        Route::get('/attendee/dashboard', [AttendeeController::class, 'getDashboardData']);
        Route::get('/attendee/tickets/{ticketId}/download', [AttendeeController::class, 'downloadTicket']);
    });

    // User Profile Routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
});

Route::get('/weather/forecast', [WeatherController::class, 'getForecast']);
