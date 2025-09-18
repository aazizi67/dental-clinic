@extends('layouts.app')

@section('title', 'نوبت جدید')

@php
function toPersianDigits($string) {
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($english, $persian, $string);
}
@endphp

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">نوبت جدید</h1>
        <p class="text-muted mb-0">ایجاد نوبت جدید برای بیمار</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">اطلاعات نوبت</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('appointments.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="patient_search" class="form-label">بیمار <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" class="form-control @error('patient_id') is-invalid @enderror" 
                                       id="patient_search" placeholder="جستجوی بیمار با نام یا شماره تماس" autocomplete="off">
                                <input type="hidden" id="patient_id" name="patient_id" value="{{ old('patient_id') }}" required>
                                <div class="dropdown-menu w-100" id="patient_dropdown" style="display: none; max-height: 200px; overflow-y: auto;">
                                    <!-- نتایج جستجو اینجا نمایش داده می‌شود -->
                                </div>
                            </div>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="doctor_id" class="form-label">دکتر <span class="text-danger">*</span></label>
                            <select class="form-select @error('doctor_id') is-invalid @enderror" id="doctor_id" name="doctor_id" required>
                                <option value="">انتخاب دکتر</option>
                                @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="appointment_date_fa" class="form-label">تاریخ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('appointment_date') is-invalid @enderror" 
                                       id="appointment_date_fa" placeholder="مثلاً ۱۴۰2/07/12" data-persian-digits="true" autocomplete="off" readonly>
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            </div>
                            <input type="hidden" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required>
                            @error('appointment_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="start_time" class="form-label">زمان شروع <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" id="start_minute">
                                    <option value="">دقیقه</option>
                                    @for($m = 0; $m <= 59; $m += 15)
                                        <option value="{{ sprintf('%02d', $m) }}">{{ toPersianDigits(sprintf('%02d', $m)) }}</option>
                                    @endfor
                                </select>
                                <span class="input-group-text">:</span>
                                <select class="form-select" id="start_hour">
                                    <option value="">ساعت</option>
                                    @for($h = 8; $h <= 20; $h++)
                                        <option value="{{ sprintf('%02d', $h) }}">{{ toPersianDigits(sprintf('%02d', $h)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <input type="hidden" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="end_time" class="form-label">زمان پایان <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" id="end_minute" disabled>
                                    <option value="">دقیقه</option>
                                </select>
                                <span class="input-group-text">:</span>
                                <select class="form-select" id="end_hour" disabled>
                                    <option value="">ساعت</option>
                                </select>
                            </div>
                            <input type="hidden" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">نوع نوبت <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">انتخاب نوع نوبت</option>
                            <option value="consultation" {{ old('type') == 'consultation' ? 'selected' : '' }}>مشاوره</option>
                            <option value="treatment" {{ old('type') == 'treatment' ? 'selected' : '' }}>درمان</option>
                            <option value="follow_up" {{ old('type') == 'follow_up' ? 'selected' : '' }}>پیگیری</option>
                            <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>اورژانس</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="chief_complaint" class="form-label">شکایت اصلی</label>
                        <textarea class="form-control @error('chief_complaint') is-invalid @enderror" 
                                  id="chief_complaint" name="chief_complaint" rows="3" 
                                  placeholder="شرح مشکل یا دلیل مراجعه بیمار">{{ old('chief_complaint') }}</textarea>
                        @error('chief_complaint')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">یادداشت‌ها</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="یادداشت‌های اضافی">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                            انصراف
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            ثبت نوبت
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.datepicker-container { position: relative; display: inline-block; width: 100%; }
.datepicker { position: absolute; top: 100%; right: 0; left: 0; background: white; border: 2px solid #e0e0e0; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); z-index: 1000; padding: 20px; display: none; direction: rtl; margin-top: 5px; font-family: 'Vazirmatn', 'Tahoma', sans-serif; }
.datepicker.show { display: block; }
.datepicker-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.nav-button { background: #4CAF50; color: white; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; font-size: 16px; font-weight: bold; }
.nav-button:hover { background: #45a049; }
.current-month-year { font-weight: bold; font-size: 18px; color: #333; text-align: center; flex: 1; }
.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; margin-bottom: 15px; }
.day-header { text-align: center; font-weight: bold; color: #666; padding: 8px; font-size: 14px; background: #f8f9fa; border-radius: 6px; }
.day-cell { text-align: center; padding: 10px; cursor: pointer; border-radius: 6px; font-size: 14px; min-height: 35px; display: flex; align-items: center; justify-content: center; transition: background-color 0.2s; }
.day-cell:hover { background: #e8f5e8; }
.day-cell.other-month { color: #ccc; cursor: default; }
.day-cell.other-month:hover { background: transparent; }
.day-cell.selected { background: #4CAF50; color: white; font-weight: bold; }
.day-cell.today { background: #2196F3; color: white; font-weight: bold; }
.day-cell.selected.today { background: #4CAF50; }
.datepicker-footer { display: flex; justify-content: space-between; gap: 10px; }
.footer-button { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
.today-button { background: #2196F3; color: white; }
.today-button:hover { background: #1976D2; }
.close-button { background: #f44336; color: white; }
.close-button:hover { background: #d32f2f; }
.position-relative .dropdown-menu { position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; border: 1px solid #dee2e6; border-radius: 0.375rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); background-color: white; }
.dropdown-item { padding: 0.75rem 1rem; border-bottom: 1px solid #f8f9fa; transition: all 0.2s ease; }
.dropdown-item:hover { background-color: #f8f9fa; color: #495057; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Initializing Persian Date Picker for appointments...');
    
    // Initialize appointment date picker
    const appointmentDatePicker = new PersianDatePicker(document.getElementById('appointment_date_fa'));
    window.currentDatePicker = appointmentDatePicker;
    
    console.log('Appointment Persian Date Picker initialized successfully!');
});
    
    function toPersianDigits(str) { const persianDigits = '۰۱۲۳۴۵۶۷۸۹'; const englishDigits = '0123456789'; return str.replace(/[0-9]/g, function(w) { return persianDigits[englishDigits.indexOf(w)]; }); }
    
    // Patient search
    let searchTimeout;
    $('#patient_search').on('input', function() { const query = $(this).val(); const dropdown = $('#patient_dropdown'); clearTimeout(searchTimeout); if (query.length < 2) { dropdown.hide(); return; } searchTimeout = setTimeout(function() { $.ajax({ url: '/api/search-patients', method: 'GET', data: { q: query }, success: function(response) { dropdown.empty(); if (response.length === 0) { dropdown.append('<div class="dropdown-item-text text-muted">هیچ بیماری یافت نشد</div>'); } else { response.forEach(function(patient) { const item = $(`<a href="#" class="dropdown-item patient-item" data-id="${patient.id}"><div class="d-flex justify-content-between"><div><strong>${patient.full_name}</strong><br><small class="text-muted">${patient.phone}</small></div><small class="text-muted">${patient.national_code || ''}</small></div></a>`); dropdown.append(item); }); } dropdown.show(); }, error: function() { dropdown.empty().append('<div class="dropdown-item-text text-danger">خطا در جستجو</div>'); dropdown.show(); } }); }, 300); });
    $(document).on('click', '.patient-item', function(e) { e.preventDefault(); $('#patient_id').val($(this).data('id')); $('#patient_search').val($(this).find('strong').text()); $('#patient_dropdown').hide(); });
    $(document).click(function(e) { if (!$(e.target).closest('.position-relative').length) $('#patient_dropdown').hide(); });
    
    // Time management
    function updateStartTime() { const hour = $('#start_hour').val(); const minute = $('#start_minute').val(); if (hour && minute) { $('#start_time').val(hour + ':' + minute); updateEndTimeOptions(); } else { $('#start_time').val(''); disableEndTime(); } }
    function updateEndTime() { const hour = $('#end_hour').val(); const minute = $('#end_minute').val(); if (hour && minute) $('#end_time').val(hour + ':' + minute); else $('#end_time').val(''); }
    function disableEndTime() { $('#end_hour, #end_minute').prop('disabled', true).val(''); $('#end_time').val(''); }
    function updateEndTimeOptions() { const startHour = parseInt($('#start_hour').val()); const startMinute = parseInt($('#start_minute').val()); if (isNaN(startHour) || isNaN(startMinute)) { disableEndTime(); return; } let minEndMinutes = (startHour * 60) + startMinute + 15; $('#end_hour, #end_minute').prop('disabled', false); $('#end_hour').empty().append('<option value="">ساعت</option>'); $('#end_minute').empty().append('<option value="">دقیقه</option>'); const minEndHour = Math.floor(minEndMinutes / 60); for (let h = minEndHour; h <= 20; h++) { const hourText = toPersianDigits(String(h).padStart(2, '0')); $('#end_hour').append(`<option value="${String(h).padStart(2, '0')}">${hourText}</option>`); } updateEndMinuteOptions(); }
    function updateEndMinuteOptions() { const selectedEndHour = parseInt($('#end_hour').val()); const startHour = parseInt($('#start_hour').val()); const startMinute = parseInt($('#start_minute').val()); if (isNaN(startHour) || isNaN(startMinute)) return; const minEndMinutes = (startHour * 60) + startMinute + 15; $('#end_minute').empty().append('<option value="">دقیقه</option>'); if (isNaN(selectedEndHour)) { for (let m = 0; m <= 45; m += 15) { const minuteText = toPersianDigits(String(m).padStart(2, '0')); $('#end_minute').append(`<option value="${String(m).padStart(2, '0')}">${minuteText}</option>`); } return; } const selectedHourMinutes = selectedEndHour * 60; for (let m = 0; m <= 45; m += 15) { const totalMinutes = selectedHourMinutes + m; if (totalMinutes >= minEndMinutes && totalMinutes <= (20 * 60 + 45)) { const minuteText = toPersianDigits(String(m).padStart(2, '0')); $('#end_minute').append(`<option value="${String(m).padStart(2, '0')}">${minuteText}</option>`); } } }
    
    $('#start_hour, #start_minute').on('change', updateStartTime);
    $('#end_hour').on('change', function() { updateEndMinuteOptions(); updateEndTime(); });
    $('#end_minute').on('change', updateEndTime);
});
</script>
@endpush
