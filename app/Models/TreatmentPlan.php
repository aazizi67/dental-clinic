<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'doctor_id', 'title', 'description', 
        'total_cost', 'paid_amount', 'status'
    ];

    protected $casts = [
        'total_cost' => 'decimal:0',
        'paid_amount' => 'decimal:0',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function treatmentItems()
    {
        return $this->hasMany(TreatmentItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_cost - $this->paid_amount;
    }

    public function updateTotalCost()
    {
        $this->total_cost = $this->treatmentItems()->sum('cost');
        $this->save();
    }
}