<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_plan_id', 'tooth_number', 'treatment_type_id',
        'treatment_type', 'cost', 'status', 'completed_at', 'notes'
    ];

    protected $casts = [
        'cost' => 'decimal:0',
        'completed_at' => 'datetime',
    ];

    public function treatmentPlan()
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    public function treatmentType()
    {
        return $this->belongsTo(TreatmentType::class);
    }

    public function getToothPositionAttribute()
    {
        $tooth = $this->tooth_number;
        
        if ($tooth >= 11 && $tooth <= 18) {
            return 'فک بالا راست';
        } elseif ($tooth >= 21 && $tooth <= 28) {
            return 'فک بالا چپ';
        } elseif ($tooth >= 31 && $tooth <= 38) {
            return 'فک پایین چپ';
        } elseif ($tooth >= 41 && $tooth <= 48) {
            return 'فک پایین راست';
        }
        
        return 'نامشخص';
    }
}