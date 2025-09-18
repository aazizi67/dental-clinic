<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaboratoryTransaction extends Model
{
    protected $fillable = [
        'laboratory_id',
        'patient_id',
        'doctor_id',
        'date',
        'time',
        'type',
        'category',
        'description',
        'price'
    ];
    
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i:s',
        'price' => 'decimal:2'
    ];
    
    // Relationship with laboratory
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
    
    // Relationship with patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    // Relationship with doctor (user)
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }
    
    // Scope for entry transactions (work sent to lab)
    public function scopeEntries($query)
    {
        return $query->where('type', 'entry');
    }
    
    // Scope for exit transactions (work received from lab)
    public function scopeExits($query)
    {
        return $query->where('type', 'exit');
    }
    
    // Scope for specific category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
    
    // Accessor to get Persian date
    public function getPersianDateAttribute()
    {
        return verta($this->date)->format('Y/m/d');
    }
    
    // Accessor to get Persian time
    public function getPersianTimeAttribute()
    {
        return verta($this->created_at)->format('H:i');
    }
}