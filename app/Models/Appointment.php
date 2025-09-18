<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_date', 'appointment_time',
        'start_time', 'end_time', 'duration', 'type', 'status', 'chief_complaint', 'notes',
        'reminder_sent', 'created_by',
        // Fields for simplified appointment system
        'patient_name', 'patient_phone'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'reminder_sent' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isToday()
    {
        return $this->appointment_date->isToday();
    }

    public function isPast()
    {
        $appointmentDateTime = Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time->format('H:i:s'));
        return $appointmentDateTime->isPast();
    }
}