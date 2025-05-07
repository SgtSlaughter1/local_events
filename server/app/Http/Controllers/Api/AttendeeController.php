<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
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

        // Get user's tickets
        $tickets = Ticket::with(['event'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'totalTickets' => $tickets->count(),
            'upcomingEvents' => $upcomingEvents->count(),
            'pastEvents' => Ticket::where('user_id', $user->id)
                ->whereHas('event', function ($query) {
                    $query->where('end_date', '<', now());
                })
                ->count()
        ];

        return response()->json([
            'stats' => $stats,
            'upcomingEvents' => $upcomingEvents,
            'tickets' => $tickets
        ]);
    }

    public function downloadTicket($ticketId)
    {
        $ticket = Ticket::with(['event', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($ticketId);
        // For now, return a simple response
        return response()->json([
            'message' => 'Ticket download functionality will be implemented soon',
            'ticket' => $ticket
        ]);
    }
}
