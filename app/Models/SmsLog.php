<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone', 'message', 'type', 'status', 'response', 'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}