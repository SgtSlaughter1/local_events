<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Scope to exclude admin
    public function scopeWithoutAdmin($query)
    {
        return $query->whereRaw('LOWER(name) != ?', ['admin']);
    }
}
