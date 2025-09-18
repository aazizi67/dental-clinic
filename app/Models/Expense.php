<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Expense extends Model
{
    protected $fillable = [
        'category',
        'description',
        'amount',
        'payment_method',
        'status',
        'check_number',
        'check_date',
        'sayad_id',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'check_date' => 'date',
    ];

    // روابط
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getCategoryNameAttribute(): string
    {
        $categories = [
            'dental_materials' => 'مواد دندانی',
            'equipment' => 'تجهیزات',
            'laboratory' => 'لابراتوار',
            'rent' => 'اجاره',
            'utilities' => 'آب و برق و گاز',
            'marketing' => 'تبلیغات',
            'other' => 'سایر'
        ];
        
        return $categories[$this->category] ?? 'نامشخص';
    }

    public function getCategoryColorAttribute(): string
    {
        $colors = [
            'dental_materials' => 'primary',
            'equipment' => 'success',
            'laboratory' => 'warning',
            'rent' => 'danger',
            'utilities' => 'info',
            'marketing' => 'secondary',
            'other' => 'dark'
        ];
        
        return $colors[$this->category] ?? 'secondary';
    }

    public function getPaymentMethodNameAttribute(): string
    {
        $methods = [
            'cash' => 'نقدی',
            'card' => 'کارت',
            'check' => 'چک',
            'bank_transfer' => 'انتقال بانکی'
        ];
        
        return $methods[$this->payment_method] ?? 'نامشخص';
    }

    public function getPersianCreatedAtAttribute(): string
    {
        return $this->created_at ? 
            Carbon::parse($this->created_at)->toDateString() : 
            '';
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        if ($fromDate) {
            $query->where('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('created_at', '<=', $toDate . ' 23:59:59');
        }
        return $query;
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount)
    {
        if ($minAmount) {
            $query->where('amount', '>=', $minAmount);
        }
        if ($maxAmount) {
            $query->where('amount', '<=', $maxAmount);
        }
        return $query;
    }
}
