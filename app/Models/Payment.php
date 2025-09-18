<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'treatment_plan_id', 'appointment_id', 'amount',
        'payment_method', 'reference_number', 'description', 'received_by', 'payment_date'
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'payment_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatmentPlan()
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}