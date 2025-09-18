<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'tooth_number', 'treatment_type_id', 'price'
    ];

    protected $casts = [
        'price' => 'decimal:0',
    ];

    public function treatmentType()
    {
        return $this->belongsTo(TreatmentType::class);
    }
}