<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_date',
        'end_date',
        'location',
        'capacity',
        'price',
        'status',
        'category_id',
        'created_by',
        'image',
        'is_online',
        'online_link'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'capacity' => 'integer',
        'is_online' => 'boolean',
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
}
