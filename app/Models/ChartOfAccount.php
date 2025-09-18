<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'code',
        'name', 
        'type',
        'parent_id',
        'description',
        'is_active',
        'level',
        'balance'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'balance' => 'decimal:2'
    ];

    // رابطه با حساب والد
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    // رابطه با حساب‌های فرعی
    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    // رابطه با تراکنش‌ها
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    // محاسبه مانده حساب
    public function calculateBalance()
    {
        $income = $this->transactions()->where('type', 'income')->sum('amount');
        $expense = $this->transactions()->where('type', 'expense')->sum('amount');
        
        return $income - $expense;
    }

    // دریافت نام کامل با نوع
    public function getFullNameAttribute()
    {
        $typeNames = [
            'asset' => 'دارایی',
            'liability' => 'بدهی',
            'equity' => 'حقوق صاحبان',
            'income' => 'درآمد',
            'expense' => 'هزینه'
        ];
        
        return $this->code . ' - ' . $this->name . ' (' . ($typeNames[$this->type] ?? $this->type) . ')';
    }
}
