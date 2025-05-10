<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventRegistrationController extends Controller
{
    /**
     * Get available tickets for an event
     */
    public function getAvailableTickets(Event $event)
    {
        $availableTickets = $event->capacity - $event->registrations()->where('status', '!=', 'cancelled')->sum('number_of_tickets');

        return response()->json([
            'success' => true,
            'data' => [
                'available_tickets' => $availableTickets,
                'price' => $event->price
            ]
        ]);
    }

    /**
     * Register for an event
     */
    public function register(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'number_of_tickets' => 'required|integer|min:1|max:' . $event->capacity,
            'payment_method' => 'required|string|in:card,bank_transfer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create registration
            $registration = EventRegistration::create([
                'user_id' => $request->user()->id,
                'event_id' => $event->id,
                'number_of_tickets' => $request->number_of_tickets,
                'payment_amount' => $event->price * $request->number_of_tickets,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'payment_status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => $registration->load(['event', 'user'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's event registrations
     */
    public function getUserRegistrations(Request $request)
    {
        $registrations = EventRegistration::with(['event'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $registrations
        ]);
    }

    /**
     * Get specific registration details
     */
    public function show(Request $request, EventRegistration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $registration->load('event')
        ]);
    }

    /**
     * Cancel registration
     */
    public function cancel(Request $request, EventRegistration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($registration->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Registration is already cancelled'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $registration->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel registration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment for registration
     */
    public function processPayment(Request $request, EventRegistration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($registration->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment already processed'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update registration status
            $registration->update([
                'status' => 'confirmed',
                'payment_status' => 'paid'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $registration->load('event')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get registration by ticket reference
     */
    public function getByTicketReference($ticketReference)
    {
        $registration = EventRegistration::with(['event', 'user'])
            ->where('ticket_reference', $ticketReference)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $registration
        ]);
    }
}
