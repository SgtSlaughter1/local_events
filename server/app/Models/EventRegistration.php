<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_reference',
        'number_of_tickets',
        'payment_amount',
        'payment_method',
        'status',
        'payment_status',
        'notes'
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            $registration->ticket_reference = static::generateTicketReference();
        });
    }

    public static function generateTicketReference()
    {
        do {
            $reference = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('ticket_reference', $reference)->exists());

        return $reference;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
