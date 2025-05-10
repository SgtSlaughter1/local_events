<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendeeController extends Controller
{
    public function getDashboardData()
    {
        $user = Auth::user();

        // Get upcoming events
        $upcomingEvents = Event::where('start_date', '>', now())
            ->where('status', 'published')
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get();

        // Calculate statistics
        $stats = [
            'totalTickets' => 0,
            'upcomingEvents' => $upcomingEvents->count(),
            'pastEvents' => 0
        ];

        return response()->json([
            'stats' => $stats,
            'upcomingEvents' => $upcomingEvents
        ]);
    }
}
