<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Staff;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'staff_id',
        'date',
        'check_in_time',
        'check_out_time',
        'check_in_method',
        'check_out_method',
        'notes'
    ];
    
    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i:s',
        'check_out_time' => 'datetime:H:i:s'
    ];
    
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
    
    // Check if the staff has checked in today
    public function isCheckedIn()
    {
        return !is_null($this->check_in_time);
    }
    
    // Check if the staff has checked out today
    public function isCheckedOut()
    {
        return !is_null($this->check_out_time);
    }
    
    // Calculate the total working hours for the day
    public function getWorkingHours()
    {
        if (!$this->isCheckedIn() || !$this->isCheckedOut()) {
            return 0;
        }
        
        $checkIn = strtotime($this->check_in_time);
        $checkOut = strtotime($this->check_out_time);
        
        $diff = $checkOut - $checkIn;
        return $diff / 3600; // Return hours
    }
    
    // Calculate salary for this day based on hourly rate
    public function getDailySalary()
    {
        if (!$this->staff || !$this->isCheckedIn() || !$this->isCheckedOut()) {
            return 0;
        }
        
        return $this->getWorkingHours() * $this->staff->hourly_rate;
    }
}