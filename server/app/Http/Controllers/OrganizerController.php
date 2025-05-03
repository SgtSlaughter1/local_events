<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventRegistration;

class OrganizerController extends Controller
{
    // 1. List My Events
    public function index(Request $request)
    {
        $user = Auth::user();
        $events = Event::where('created_by', $user->id)->latest()->get();
        return response()->json(['events' => $events]);
    }

    // 2. Create Event
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string',
            'is_online' => 'boolean',
            'online_link' => 'nullable|string',
        ]);
        $validated['created_by'] = $user->id;
        $event = Event::create($validated);
        return response()->json(['event' => $event], 201);
    }

    // 3. View Single Event
    public function show($id)
    {
        $user = Auth::user();
        $event = Event::where('id', $id)->where('created_by', $user->id)->firstOrFail();
        return response()->json(['event' => $event]);
    }

    // 4. Update Event
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $event = Event::where('id', $id)->where('created_by', $user->id)->firstOrFail();
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|string',
            'is_online' => 'boolean',
            'online_link' => 'nullable|string',
        ]);
        $event->update($validated);
        return response()->json(['event' => $event]);
    }

    // 5. Delete Event
    public function destroy($id)
    {
        $user = Auth::user();
        $event = Event::where('id', $id)->where('created_by', $user->id)->firstOrFail();
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }

    // 6. List Registrations for My Event
    public function registrations($eventId)
    {
        $user = Auth::user();
        $event = Event::where('id', $eventId)->where('created_by', $user->id)->firstOrFail();
        $registrations = $event->registrations()->with('user')->get();
        return response()->json(['registrations' => $registrations]);
    }

    // 7. Update Registration Status
    public function updateRegistration(Request $request, $registrationId)
    {
        $user = Auth::user();
        $registration = EventRegistration::findOrFail($registrationId);
        $event = $registration->event;
        if ($event->created_by !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);
        $registration->update($validated);
        return response()->json(['registration' => $registration]);
    }

    // 8. (Optional) Event Stats
    public function stats($eventId)
    {
        $user = Auth::user();
        $event = Event::where('id', $eventId)->where('created_by', $user->id)->firstOrFail();
        $registrationsCount = $event->registrations()->count();
        $reviewsCount = $event->reviews()->count();
        $averageRating = $event->reviews()->avg('rating');
        return response()->json([
            'registrations_count' => $registrationsCount,
            'reviews_count' => $reviewsCount,
            'average_rating' => $averageRating,
        ]);
    }
}
