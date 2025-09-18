@extends('layouts.app')

@section('title', 'افزودن پرسنل جدید')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">افزودن پرسنل جدید</h1>
        <p class="text-muted mb-0">ثبت اطلاعات پرسنل جدید کلینیک</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('staff.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            بازگشت به لیست پرسنل
        </a>
    </div>
</div>

<!-- Staff Creation Form -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">فرم ثبت پرسنل</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('staff.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                               id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">نام خانوادگی <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                               id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="national_id" class="form-label">کد ملی</label>
                        <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                               id="national_id" name="national_id" value="{{ old('national_id') }}" 
                               placeholder="1234567890" maxlength="10">
                        @error('national_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">تلفن <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="role" class="form-label">سمت <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">انتخاب سمت</option>
                            <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>دکتر</option>
                            <option value="secretary" {{ old('role') == 'secretary' ? 'selected' : '' }}>منشی</option>
                            <option value="assistant" {{ old('role') == 'assistant' ? 'selected' : '' }}>کمک‌کار</option>
                            <option value="nurse" {{ old('role') == 'nurse' ? 'selected' : '' }}>پرستار</option>
                            <option value="cleaner" {{ old('role') == 'cleaner' ? 'selected' : '' }}>نگهبان/تمیزکار</option>
                            <option value="other" {{ old('role') == 'other' ? 'selected' : '' }}>سایر</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="hourly_rate" class="form-label">حقوق ساعتی (تومان) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('hourly_rate') is-invalid @enderror" 
                               id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}" 
                               min="0" step="1000" required>
                        @error('hourly_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="hire_date" class="form-label">تاریخ استخدام</label>
                        <input type="text" class="form-control @error('hire_date') is-invalid @enderror" 
                               id="hire_date" name="hire_date" value="{{ old('hire_date') }}" 
                               placeholder="مثلاً ۱۴۰۳/۰۹/۲۲" autocomplete="off" readonly>
                        <input type="hidden" id="hire_date_gregorian" name="hire_date" value="{{ old('hire_date') }}">
                        @error('hire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="birth_date" class="form-label">تاریخ تولد</label>
                        <input type="text" class="form-control @error('birth_date') is-invalid @enderror" 
                               id="birth_date" name="birth_date" value="{{ old('birth_date') }}" 
                               placeholder="مثلاً ۱۴۰۳/۰۹/۲۲" autocomplete="off" readonly>
                        <input type="hidden" id="birth_date_gregorian" name="birth_date" value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label">نام تماس اضطراری</label>
                        <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                               id="emergency_contact_name" name="emergency_contact_name" 
                               value="{{ old('emergency_contact_name') }}">
                        @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="emergency_contact_phone" class="form-label">تلفن تماس اضطراری</label>
                        <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                               id="emergency_contact_phone" name="emergency_contact_phone" 
                               value="{{ old('emergency_contact_phone') }}">
                        @error('emergency_contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="mb-3">
                        <label for="address" class="form-label">آدرس</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            وضعیت فعال بودن پرسنل
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('staff.index') }}" class="btn btn-secondary">انصراف</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    ذخیره اطلاعات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    $('#hire_date').MdPersianDateTimePicker({
        targetTextSelector: '#hire_date',
        targetDateSelector: '#hire_date_gregorian',
        dateFormat: 'yyyy/MM/dd',
        isGregorian: false,
        modalMode: false,
        englishNumber: false,
        enableTimePicker: false
    });
    
    $('#birth_date').MdPersianDateTimePicker({
        targetTextSelector: '#birth_date',
        targetDateSelector: '#birth_date_gregorian',
        dateFormat: 'yyyy/MM/dd',
        isGregorian: false,
        modalMode: false,
        englishNumber: false,
        enableTimePicker: false
    });
});
</script>
@endpush