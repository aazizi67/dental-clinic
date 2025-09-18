<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    // Relationship with laboratory transactions
    public function transactions()
    {
        return $this->hasMany(LaboratoryTransaction::class);
    }
    
    // Scope for active laboratories
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}