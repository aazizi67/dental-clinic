<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'type',
        'category',
        'patient_id',
        'treatment_plan_id',
        'amount',
        'account_id',
        'payment_method',
        'check_number',
        'check_date',
        'check_bank',
        'sayad_id',
        'check_status',
        'description',
        'notes',
        'status',
        'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'check_date' => 'date',
        'amount' => 'decimal:2'
    ];

    // رابطه با بیمار
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // رابطه با طرح درمان
    public function treatmentPlan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    // رابطه با حساب
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    // رابطه با کاربر ثبت کننده
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // تولید شماره تراکنش خودکار
    public static function generateTransactionNumber()
    {
        $date = now()->format('Ymd');
        $lastTransaction = self::where('transaction_number', 'like', 'TR' . $date . '%')
                              ->orderBy('transaction_number', 'desc')
                              ->first();
        
        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->transaction_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'TR' . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // دریافت نام نحوه پرداخت به فارسی
    public function getPaymentMethodNameAttribute()
    {
        $methods = [
            'cash' => 'نقدی',
            'card' => 'کارت به کارت',
            'pos' => 'پوز',
            'bank_transfer' => 'انتقال بانکی',
            'check' => 'چک'
        ];
        
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    // دریافت نام دسته بندی به فارسی
    public function getCategoryNameAttribute()
    {
        $categories = [
            'patient_payment' => 'پرداخت بیمار',
            'dental_materials' => 'مواد دندانی',
            'equipment' => 'تجهیزات',
            'laboratory' => 'لابراتوار',
            'other' => 'سایر'
        ];
        
        return $categories[$this->category] ?? $this->category;
    }
}
