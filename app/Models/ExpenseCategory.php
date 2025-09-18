<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'account_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // رابطه با حساب
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
