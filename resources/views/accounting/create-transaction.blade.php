@extends('layouts.app')

@section('title', 'تراکنش جدید')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">تراکنش جدید</h1>
        <p class="text-muted mb-0">ثبت درآمد یا هزینه جدید</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('accounting.transactions') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">اطلاعات تراکنش</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('accounting.transactions.store') }}" id="transactionForm">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="transaction_date" class="form-label">تاریخ تراکنش <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" 
                                   id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                            @error('transaction_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="type" class="form-label">نوع تراکنش <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">انتخاب کنید</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>درآمد</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>هزینه</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">انتخاب کنید</option>
                                <option value="patient_payment" {{ old('category') == 'patient_payment' ? 'selected' : '' }}>پرداخت بیمار</option>
                                <option value="dental_materials" {{ old('category') == 'dental_materials' ? 'selected' : '' }}>مواد دندانی</option>
                                <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>تجهیزات</option>
                                <option value="laboratory" {{ old('category') == 'laboratory' ? 'selected' : '' }}>لابراتوار</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>سایر</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="amount" class="form-label">مبلغ (ریال) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount') }}" min="0" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="account_id" class="form-label">حساب <span class="text-danger">*</span></label>
                        <select class="form-select @error('account_id') is-invalid @enderror" id="account_id" name="account_id" required>
                            <option value="">انتخاب حساب</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- بخش اطلاعات بیمار (فقط برای پرداخت بیمار) -->
                    <div id="patient_section" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="patient_id" class="form-label">بیمار <span class="text-danger">*</span></label>
                                <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id">
                                    <option value="">انتخاب بیمار</option>
                                    <!-- بیماران با AJAX بارگذاری می‌شوند -->
                                </select>
                                @error('patient_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="treatment_plan_id" class="form-label">طرح درمان</label>
                                <select class="form-select" id="treatment_plan_id" name="treatment_plan_id">
                                    <option value="">انتخاب طرح درمان</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">روش پرداخت <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">انتخاب کنید</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدی</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>کارت به کارت</option>
                                <option value="pos" {{ old('payment_method') == 'pos' ? 'selected' : '' }}>پوز</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>انتقال بانکی</option>
                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>چک</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- بخش اطلاعات چک -->
                    <div id="check_section" style="display: none;">
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">اطلاعات چک</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="check_number" class="form-label">شماره چک <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('check_number') is-invalid @enderror" 
                                               id="check_number" name="check_number" value="{{ old('check_number') }}">
                                        @error('check_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="check_date" class="form-label">تاریخ چک <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('check_date') is-invalid @enderror" 
                                               id="check_date" name="check_date" value="{{ old('check_date') }}">
                                        @error('check_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="check_bank" class="form-label">بانک صادرکننده <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('check_bank') is-invalid @enderror" 
                                               id="check_bank" name="check_bank" value="{{ old('check_bank') }}" placeholder="نام بانک">
                                        @error('check_bank')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="sayad_id" class="form-label">شناسه صیاد <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('sayad_id') is-invalid @enderror" 
                                               id="sayad_id" name="sayad_id" value="{{ old('sayad_id') }}" placeholder="شناسه صیاد">
                                        @error('sayad_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">توضیحات</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" placeholder="توضیحات تراکنش">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">یادداشت</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="2" placeholder="یادداشت‌های داخلی">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>
                            ثبت تراکنش
                        </button>
                        <a href="{{ route('accounting.transactions') }}" class="btn btn-secondary">
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">راهنمای پرداخت</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">روش‌های پرداخت:</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-cash me-2"></i>نقدی</li>
                        <li><i class="bi bi-credit-card me-2"></i>کارت به کارت</li>
                        <li><i class="bi bi-credit-card-2-front me-2"></i>پوز</li>
                        <li><i class="bi bi-bank me-2"></i>انتقال بانکی</li>
                        <li><i class="bi bi-journal-check me-2"></i>چک</li>
                    </ul>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>نکته:</strong> برای پرداخت با چک، تکمیل اطلاعات چک الزامی است.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // نمایش/مخفی کردن بخش‌های مختلف بر اساس انتخاب‌ها
    $('#category').change(function() {
        const category = $(this).val();
        if (category === 'patient_payment') {
            $('#patient_section').show();
            loadPatients();
        } else {
            $('#patient_section').hide();
        }
    });
    
    $('#payment_method').change(function() {
        const method = $(this).val();
        if (method === 'check') {
            $('#check_section').show();
        } else {
            $('#check_section').hide();
        }
    });
    
    // بارگذاری لیست بیماران
    function loadPatients() {
        $.ajax({
            url: '/api/search-patients',
            method: 'GET',
            data: { q: '' },
            success: function(response) {
                const select = $('#patient_id');
                select.empty().append('<option value="">انتخاب بیمار</option>');
                
                response.forEach(function(patient) {
                    select.append(`<option value="${patient.id}">${patient.full_name} - ${patient.phone}</option>`);
                });
            }
        });
    }
    
    // فرمت کردن مبلغ
    $('#amount').on('input', function() {
        let value = $(this).val().replace(/,/g, '');
        if (value) {
            $(this).val(parseInt(value).toLocaleString());
        }
    });
    
    // اعتبارسنجی فرم
    $('#transactionForm').submit(function(e) {
        const paymentMethod = $('#payment_method').val();
        const category = $('#category').val();
        
        if (paymentMethod === 'check') {
            const checkFields = ['check_number', 'check_date', 'check_bank', 'sayad_id'];
            let hasError = false;
            
            checkFields.forEach(function(field) {
                if (!$('#' + field).val()) {
                    $('#' + field).addClass('is-invalid');
                    hasError = true;
                } else {
                    $('#' + field).removeClass('is-invalid');
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('لطفا تمام اطلاعات چک را کامل کنید');
                return false;
            }
        }
        
        if (category === 'patient_payment' && !$('#patient_id').val()) {
            e.preventDefault();
            alert('لطفا بیمار را انتخاب کنید');
            return false;
        }
        
        // تبدیل مبلغ به عدد
        const amount = $('#amount').val().replace(/,/g, '');
        $('#amount').val(amount);
    });
});
</script>
@endpush