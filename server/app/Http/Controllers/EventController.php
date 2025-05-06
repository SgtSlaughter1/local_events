<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\EventRegistration;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class EventController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function index(Request $request)
    {
        $query = Event::with(['category', 'creator'])
            ->when($request->category_id, function ($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->when($request->search, function ($q) use ($request) {
                return $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->is_online !== null, function ($q) use ($request) {
                return $q->where('is_online', $request->is_online);
            });

        if ($request->sort === 'upcoming') {
            $query->upcoming();
        }

        $events = $query->latest()->paginate(12);

        // Append image_url to each event
        $events->through(function ($event) {
            return $event->append('image_url');
        });

        return response()->json([
            'events' => $events,
            'categories' => Category::active()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required_if:is_online,false|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'is_online' => 'boolean',
            'online_link' => 'required_if:is_online,true|nullable|url'
        ]);

        $event = new Event($request->except('image'));
        $event->created_by = Auth::id();

        // Geocode the address if it's not an online event
        if (!$request->is_online && $request->location) {
            $coordinates = $this->geocodingService->geocodeAddress($request->location);
            if ($coordinates) {
                $event->latitude = $coordinates['latitude'];
                $event->longitude = $coordinates['longitude'];
            }
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $event->image = $path;
        }

        $event->save();

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event->load('category', 'creator')->append('image_url')
        ], 201);
    }

    public function show(Event $event)
    {
        return response()->json([
            'event' => $event->load(['category', 'creator', 'reviews.user'])->append('image_url')
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'location' => 'sometimes|required_if:is_online,false|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'is_online' => 'boolean',
            'online_link' => 'required_if:is_online,true|nullable|url'
        ]);

        // Handle image update if present
        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $path = $request->file('image')->store('events', 'public');
            $event->image = $path;
        }

        $event->update($request->except('image'));

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event->load('category', 'creator')->append('image_url')
        ]);
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }

    public function register(Event $event)
    {
        $request = request();
        $request->validate([
            'number_of_tickets' => 'required|integer|min:1'
        ]);

        if (!$event->isRegistrationOpen()) {
            return response()->json([
                'message' => 'Registration is not available for this event'
            ], 422);
        }

        // Check if there's enough capacity
        if ($event->capacity) {
            $remainingCapacity = $event->getRemainingCapacity();
            if ($remainingCapacity < $request->number_of_tickets) {
                return response()->json([
                    'message' => 'Not enough tickets available. Only ' . $remainingCapacity . ' tickets left.'
                ], 422);
            }
        }

        $registration = $event->registrations()->create([
            'user_id' => Auth::id(),
            'status' => 'pending',
            'payment_status' => $event->price > 0 ? 'pending' : 'paid',
            'payment_amount' => $event->price * $request->number_of_tickets,
            'number_of_tickets' => $request->number_of_tickets
        ]);

        return response()->json([
            'message' => 'Registration successful',
            'registration' => $registration
        ], 201);
    }

    public function myEvents()
    {
        $createdEvents = $this->getCreatedEvents();
        $registeredEvents = $this->getRegisteredEvents();

        return response()->json([
            'created_events' => $createdEvents,
            'registered_events' => $registeredEvents
        ]);
    }

    // Organizer specific methods
    public function organizerEvents()
    {
        $events = $this->getOrganizerEvents();
        return response()->json(['events' => $events]);
    }

    public function eventRegistrations(Event $event)
    {
        $this->authorize('view', $event);
        $registrations = $this->getEventRegistrations($event);
        return response()->json(['registrations' => $registrations]);
    }

    public function updateRegistration(Request $request, EventRegistration $registration)
    {
        $this->authorize('update', $registration->event);
        $this->validateRegistrationStatus($request);
        $registration = $this->updateRegistrationStatus($registration, $request->status);

        return response()->json([
            'message' => 'Registration updated successfully',
            'registration' => $registration
        ]);
    }

    public function eventStats(Event $event)
    {
        $this->authorize('view', $event);
        $stats = $this->calculateEventStats($event);
        return response()->json(['stats' => $stats]);
    }

    // Private helper methods
    private function getCreatedEvents()
    {
        return Event::where('created_by', Auth::id())
            ->with('category')
            ->latest()
            ->get()
            ->each(function ($event) {
                $event->append('image_url');
            });
    }

    private function getRegisteredEvents()
    {
        return Event::whereHas('registrations', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['category', 'registrations' => function ($query) {
            $query->where('user_id', Auth::id());
        }])
        ->latest()
        ->get()
        ->each(function ($event) {
            $event->append('image_url');
        });
    }

    private function getOrganizerEvents()
    {
        return Event::where('created_by', Auth::id())
            ->with(['category', 'registrations'])
            ->latest()
            ->get()
            ->each(function ($event) {
                $event->append('image_url');
            });
    }

    private function getEventRegistrations(Event $event)
    {
        return $event->registrations()
            ->with('user')
            ->latest()
            ->get();
    }

    private function validateRegistrationStatus(Request $request)
    {
        return $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);
    }

    private function updateRegistrationStatus(EventRegistration $registration, string $status)
    {
        $registration->update(['status' => $status]);
        return $registration;
    }

    private function calculateEventStats(Event $event)
    {
        return [
            'registrations_count' => $event->registrations()->count(),
            'reviews_count' => $event->reviews()->count(),
            'average_rating' => $event->reviews()->avg('rating'),
            'confirmed_registrations' => $event->registrations()->where('status', 'confirmed')->count(),
            'pending_registrations' => $event->registrations()->where('status', 'pending')->count(),
            'cancelled_registrations' => $event->registrations()->where('status', 'cancelled')->count(),
        ];
    }
}
