@extends('layouts.app')

@section('title', 'ثبت بیمار جدید')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">ثبت بیمار جدید</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">اطلاعات بیمار</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('patients.store') }}">
                    @csrf
                    
                    <!-- تاریخ ثبت بیمار و شماره پرونده -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="registered_at" class="form-label">تاریخ ثبت بیمار <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('registered_at') is-invalid @enderror" 
                                       id="registered_at" name="registered_at" placeholder="برای انتخاب تاریخ کلیک کنید" 
                                       data-jdp data-jdp-format="YYYY/MM/DD" data-jdp-use-persian-digits="true" autocomplete="off" required>
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            </div>
                            @error('registered_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="file_number" class="form-label">شماره پرونده</label>
                            <input type="number" class="form-control @error('file_number') is-invalid @enderror" 
                                   id="file_number" name="file_number" value="{{ old('file_number') }}" 
                                   placeholder="مثال: 1001">
                            @error('file_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- نام، نام خانوادگی و جنسیت -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="first_name" class="form-label">نام <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="last_name" class="form-label">نام خانوادگی <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="gender" class="form-label">جنسیت</label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">انتخاب کنید</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>آقا</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>خانم</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- تاریخ تولد و شماره تماس ها -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="birth_date" class="form-label">تاریخ تولد</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" name="birth_date" placeholder="برای انتخاب تاریخ کلیک کنید" 
                                       data-jdp data-jdp-format="YYYY/MM/DD" data-jdp-use-persian-digits="true" autocomplete="off">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            </div>
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="phone" class="form-label">شماره تماس <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                   placeholder="09121234567" dir="ltr" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="phone2" class="form-label">شماره تماس ۲</label>
                            <input type="text" class="form-control @error('phone2') is-invalid @enderror" 
                                   id="phone2" name="phone2" value="{{ old('phone2') }}" 
                                   placeholder="09121234567" dir="ltr">
                            @error('phone2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- توضیحات ( سابقه پزشکی) -->
                    <div class="mb-4">
                        <label for="medical_history" class="form-label">توضیحات و سابقه پزشکی</label>
                        <textarea class="form-control @error('medical_history') is-invalid @enderror" 
                                  id="medical_history" name="medical_history" rows="4" 
                                  placeholder="بیماری‌های زمینه‌ای، داروهای مصرفی، آلرژی‌ها، توضیحات اضافی...">{{ old('medical_history') }}</textarea>
                        @error('medical_history')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            ذخیره اطلاعات
                        </button>
                        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">راهنما</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <ul class="mb-0">
                        <li>فیلدهای ستاره‌دار اجباری هستند</li>
                        <li>شماره تماس باید با 09 شروع شود</li>
                        <li>کد ملی باید 10 رقم باشد</li>
                        <li>اطلاعات پزشکی مهم بیمار را در قسمت سابقه پزشکی وارد کنید</li>
                    </ul>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .date-input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        font-size: 1rem;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .date-input:focus {
        outline: none;
        border-color: #4c6ef5;
        box-shadow: 0 0 0 3px rgba(76, 110, 245, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('Patient form script loaded');
        
        // Auto-detect gender based on first name
        let genderDetectionTimeout;
        $('#first_name').on('input', function() {
            const firstName = $(this).val().trim();
            console.log('First name changed to:', firstName);
            
            // Clear previous timeout
            clearTimeout(genderDetectionTimeout);
            
            if (firstName.length > 1) {
                // Set a new timeout to debounce the requests
                genderDetectionTimeout = setTimeout(function() {
                    console.log('Sending AJAX request for:', firstName);
                    
                    // Get CSRF token from meta tag
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    console.log('CSRF Token:', csrfToken);
                    
                    // Try Gemini API first, fallback to internal detection
                    $.ajax({
                        url: '/api/gemini-detect-gender',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            first_name: firstName
                        },
                        success: function(response) {
                            console.log('Gemini gender detection response:', response);
                            if (response.gender) {
                                $('#gender').val(response.gender);
                            } else {
                                // Fallback to internal detection if Gemini fails
                                $.ajax({
                                    url: '/api/detect-gender',
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    data: {
                                        first_name: firstName
                                    },
                                    success: function(fallbackResponse) {
                                        console.log('Fallback gender detection response:', fallbackResponse);
                                        if (fallbackResponse.gender) {
                                            $('#gender').val(fallbackResponse.gender);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        // Silent fail - don't show error to user
                                        console.log('Fallback gender detection failed (silent fail):', error);
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            // If Gemini fails, try fallback
                            console.log('Gemini gender detection failed, trying fallback:', error);
                            $.ajax({
                                url: '/api/detect-gender',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                data: {
                                    first_name: firstName
                                },
                                success: function(fallbackResponse) {
                                    console.log('Fallback gender detection response:', fallbackResponse);
                                    if (fallbackResponse.gender) {
                                        $('#gender').val(fallbackResponse.gender);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    // Silent fail - don't show error to user
                                    console.log('Fallback gender detection failed (silent fail):', error);
                                }
                            });
                        }
                    });
                }, 300); // 300ms debounce
            }
        });
    });
    
    // Phone number validation
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                let value = this.value.replace(/[^0-9]/g, '');
                if (value.length > 11) {
                    value = value.substr(0, 11);
                }
                this.value = value;
                
                // بررسی فرمت شماره
                if (value.length > 0 && !value.startsWith('09')) {
                    this.classList.add('is-invalid');
                    if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'شماره تماس باید با 09 شروع شود';
                        this.parentNode.insertBefore(errorDiv, this.nextSibling);
                    }
                } else if (value.length === 11 && value.startsWith('09')) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    if (this.nextElementSibling && this.nextElementSibling.classList.contains('invalid-feedback')) {
                        this.nextElementSibling.remove();
                    }
                } else {
                    this.classList.remove('is-invalid', 'is-valid');
                    if (this.nextElementSibling && this.nextElementSibling.classList.contains('invalid-feedback')) {
                        this.nextElementSibling.remove();
                    }
                }
            });
        }
    });
    
    // بررسی اعتبار شماره تلفن ۲ (اختیاری)
    document.addEventListener('DOMContentLoaded', function() {
        const phone2Input = document.getElementById('phone2');
        if (phone2Input) {
            phone2Input.addEventListener('input', function() {
                let value = this.value.replace(/[^0-9]/g, '');
                if (value.length > 11) {
                    value = value.substr(0, 11);
                }
                this.value = value;
                
                // بررسی فرمت شماره - فقط اگر مقدار وارد شده باشد
                if (value.length > 0) {
                    if (!value.startsWith('09')) {
                        this.classList.add('is-invalid');
                        if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = 'شماره تماس ۲ باید با 09 شروع شود';
                            this.parentNode.insertBefore(errorDiv, this.nextSibling);
                        }
                    } else if (value.length === 11 && value.startsWith('09')) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                        if (this.nextElementSibling && this.nextElementSibling.classList.contains('invalid-feedback')) {
                            this.nextElementSibling.remove();
                        }
                    } else {
                        this.classList.remove('is-invalid', 'is-valid');
                        if (this.nextElementSibling && this.nextElementSibling.classList.contains('invalid-feedback')) {
                            this.nextElementSibling.remove();
                        }
                    }
                } else {
                    // اگر خالی باشد کلاس‌ها رو حذف کن (چون اختیاری است)
                    this.classList.remove('is-invalid', 'is-valid');
                    if (this.nextElementSibling && this.nextElementSibling.classList.contains('invalid-feedback')) {
                        this.nextElementSibling.remove();
                    }
                }
            });
        }
    });
    
    // اعتبارسنجی فرم قبل از ارسال
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const phone = document.getElementById('phone').value;
                const phone2 = document.getElementById('phone2').value;
                const firstName = document.getElementById('first_name').value.trim();
                const lastName = document.getElementById('last_name').value.trim();
                const registeredAt = document.getElementById('registered_at').value;
                
                let hasError = false;
                
                // بررسی نام
                if (!firstName) {
                    document.getElementById('first_name').classList.add('is-invalid');
                    hasError = true;
                }
                
                // بررسی نام خانوادگی
                if (!lastName) {
                    document.getElementById('last_name').classList.add('is-invalid');
                    hasError = true;
                }
                
                // بررسی شماره تماس ۱
                if (!phone || phone.length !== 11 || !phone.startsWith('09')) {
                    document.getElementById('phone').classList.add('is-invalid');
                    hasError = true;
                }
                
                // بررسی شماره تماس ۲ (فقط اگر وارد شده باشد)
                if (phone2 && (phone2.length !== 11 || !phone2.startsWith('09'))) {
                    document.getElementById('phone2').classList.add('is-invalid');
                    hasError = true;
                }
                
                // بررسی تاریخ ثبت
                if (!registeredAt) {
                    document.getElementById('registered_at').classList.add('is-invalid');
                    hasError = true;
                }
                
                if (hasError) {
                    e.preventDefault();
                    alert('لطفاً فیلدهای اجباری را تکمیل کنید');
                }
            });
        }
    });
</script>
@endpush