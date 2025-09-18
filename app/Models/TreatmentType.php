<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'default_price', 'description', 'is_active'
    ];

    protected $casts = [
        'default_price' => 'decimal:0',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function treatmentItems()
    {
        return $this->hasMany(TreatmentItem::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}