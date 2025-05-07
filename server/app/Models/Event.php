<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'street_address',
        'city',
        'country',
        'capacity',
        'price',
        'status',
        'category_id',
        'created_by',
        'image',
        'is_online',
        'online_link',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'capacity' => 'integer',
        'is_online' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    protected $appends = [
        'full_address',
        'city_country'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
                    ->where('status', 'active')
                    ->orderBy('start_date');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Methods
    public function isRegistrationOpen()
    {
        return $this->status === 'active' &&
               $this->start_date->isFuture() &&
               (!$this->capacity || $this->registrations()->count() < $this->capacity);
    }

    public function getRemainingCapacity()
    {
        if (!$this->capacity) return null;
        return $this->capacity - $this->registrations()->count();
    }

    public function getAverageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // If the image is already a full URL, return it
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // If it's a relative path, construct the full URL
        $fullPath = 'events/' . $this->image;
        if (!Storage::disk('public')->exists($fullPath)) {
            return null;
        }

        return asset('storage/' . $fullPath);
    }

    // Computed attributes
    public function getFullAddressAttribute()
    {
        if ($this->is_online) {
            return 'Online Event';
        }

        $parts = array_filter([
            $this->street_address,
            $this->city,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    public function getCityCountryAttribute()
    {
        if ($this->is_online) {
            return null;
        }

        return implode(', ', array_filter([
            $this->city,
            $this->country
        ]));
    }
}
