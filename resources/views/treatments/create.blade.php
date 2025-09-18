@extends('layouts.app')

@section('title', 'معاینه جدید')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">معاینه جدید</h1>
        <p class="text-muted mb-0">ثبت معاینه و طرح درمان برای بیمار</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('treatments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>
            بازگشت
        </a>
    </div>
</div>

<div class="row">
    <!-- اطلاعات بیمار و ضبط صدا -->
    <div class="col-lg-4 mb-4">
        <!-- اطلاعات بیمار -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">انتخاب بیمار</h5>
            </div>
            <div class="card-body">
                <!-- Unified patient search and creation -->
                <div class="mb-3">
                    <h5 class="mb-3">
                        <i class="bi bi-search me-2"></i>
                        جستجو یا ایجاد بیمار
                    </h5>
                    <div class="position-relative">
                        <div class="input-group">
                            <input type="text" class="form-control" id="patient_search" 
                                   placeholder="نام، نام خانوادگی یا شماره تماس بیمار" autocomplete="off">
                            <button type="button" class="btn btn-outline-primary" id="create_patient_btn" style="display: none;">
                                <i class="bi bi-plus-lg me-1"></i>
                                ایجاد بیمار
                            </button>
                        </div>
                        <div class="dropdown-menu w-100" id="patient_dropdown" style="display: none; max-height: 200px; overflow-y: auto;">
                            <!-- نتایج جستجو اینجا نمایش داده می‌شود -->
                        </div>
                    </div>
                    
                    <!-- Phone number input form (hidden by default) -->
                    <div id="phone_input_form" class="mt-3" style="display: none;">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="mb-3">لطفا شماره تماس بیمار را وارد کنید</h6>
                                <div class="mb-3">
                                    <label for="patient_phone_input" class="form-label">شماره تماس <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="patient_phone_input" 
                                           placeholder="09123456789" 
                                           inputmode="numeric"
                                           pattern="[0-9]*"
                                           maxlength="11">
                                    <div class="form-text">شماره تماس باید با 09 شروع شود</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary" onclick="createPatientWithPhone()">
                                        <i class="bi bi-check-lg me-1"></i>
                                        ایجاد و انتخاب بیمار
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="cancelPhoneInput()">
                                        انصراف
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="selected_patient" style="display: none;" class="patient-info-box border">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <span id="patient_initial"></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium" id="patient_name"></div>
                            <small class="text-muted" id="patient_phone"></small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPatientSelection()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voice Recognition Section -->
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-mic me-2"></i>
                    طرح درمان صوتی
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column align-items-center">
                    <button id="startVoiceRecognition" class="btn btn-primary btn-sm mb-2" type="button">
                        <i class="bi bi-mic-fill me-1"></i>
                        شروع ضبط صدا
                    </button>
                    <div id="voiceStatus" class="text-center mb-2" style="min-height: 20px;">
                        <span class="text-muted small">برای شروع ضبط کلیک کنید</span>
                    </div>
                    <div id="voiceResult" class="bg-light p-2 rounded w-100" style="min-height: 60px;">
                        <strong class="small">متن شناسایی شده:</strong>
                        <div id="recognizedText" class="mt-1 small" style="min-height: 30px; font-size: 0.8rem;"></div>
                    </div>
                    <div class="mt-1 text-center">
                        <small class="text-muted" style="font-size: 0.7rem;">سیستم به صورت خودکار متن شما را شناسایی کرده و طرح درمان را به‌روزرسانی می‌کند</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- چارت دندانی -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">چارت دندانی</h5>
                        <small class="text-muted">برای انتخاب دندان کلیک کنید</small>
                    </div>
                    <!-- دکمه‌های عملیات چارت -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="undo_treatment_btn" style="display: none;" onclick="undoLastTreatment()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Undo
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearDentalChart()">
                            <i class="bi bi-trash me-1"></i>
                            پاک کردن چارت
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- چارت دندانی -->
                <div class="dental-chart">
                    <!-- فک بالا -->
                    <div class="jaw-section upper-jaw mb-4">
                        <h6 class="text-center mb-3">فک بالا</h6>
                        <div class="teeth-row">
                            <div class="side-label right-label">چپ</div>
                            <!-- دندان‌های راست (1-8) -->
                            <div class="teeth-side right-side">
                                @for($i = 8; $i >= 1; $i--)
                                    @php $actualTooth = 10 + $i; @endphp
                                    <div class="tooth" data-tooth="{{ $actualTooth }}" data-jaw="upper" data-side="right" data-display="{{ $i }}">
                                        <div class="tooth-number">{{ $i }}</div>
                                        <div class="tooth-body"></div>
                                        <!-- Treatment dropdown for each tooth -->
                                        <div class="tooth-dropdown">
                                            <select class="form-select form-select-sm treatment-select">
                                                <option value="">انتخاب درمان</option>
                                                <option value="1">ترمیم</option>
                                                <option value="2">اندو</option>
                                                <option value="3">پست</option>
                                                <option value="4">روکش</option>
                                                <option value="5">اندو، پست و روکش</option>
                                                <option value="7">ایمپلنت</option>
                                                <option value="6">کشیدن</option>
                                                <option value="8">جراحی نسج نرم</option>
                                                <option value="9">جراحی نسج سخت</option>
                                                <option value="10">CL</option>
                                            </select>
                                            <div class="tooth-dropdown-actions">
                                                <button type="button" class="btn btn-outline-danger btn-remove-treatment btn-lg" style="display: none; margin: 5px auto;">−</button>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <!-- دندان‌های چپ (1-8) -->
                            <div class="teeth-side left-side">
                                @for($i = 1; $i <= 8; $i++)
                                    @php $actualTooth = 20 + $i; @endphp
                                    <div class="tooth" data-tooth="{{ $actualTooth }}" data-jaw="upper" data-side="left" data-display="{{ $i }}">
                                        <div class="tooth-number">{{ $i }}</div>
                                        <div class="tooth-body"></div>
                                        <!-- Treatment dropdown for each tooth -->
                                        <div class="tooth-dropdown">
                                            <select class="form-select form-select-sm treatment-select">
                                                <option value="">انتخاب درمان</option>
                                                <option value="1">ترمیم</option>
                                                <option value="2">اندو</option>
                                                <option value="3">پست</option>
                                                <option value="4">روکش</option>
                                                <option value="5">اندو، پست و روکش</option>
                                                <option value="7">ایمپلنت</option>
                                                <option value="6">کشیدن</option>
                                                <option value="8">جراحی نسج نرم</option>
                                                <option value="9">جراحی نسج سخت</option>
                                                <option value="10">CL</option>
                                            </select>
                                            <div class="tooth-dropdown-actions">
                                                <button type="button" class="btn btn-outline-danger btn-remove-treatment btn-lg" style="display: none; margin: 5px auto;">−</button>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <div class="side-label left-label">راست</div>
                        </div>
                    </div>

                    <!-- فک پایین -->
                    <div class="jaw-section lower-jaw">
                        <h6 class="text-center mb-3">فک پایین</h6>
                        <div class="teeth-row">
                            <div class="side-label right-label">چپ</div>
                            <!-- دندان‌های راست (1-8) -->
                            <div class="teeth-side right-side">
                                @for($i = 8; $i >= 1; $i--)
                                    @php $actualTooth = 30 + $i; @endphp
                                    <div class="tooth" data-tooth="{{ $actualTooth }}" data-jaw="lower" data-side="right" data-display="{{ $i }}">
                                        <div class="tooth-number">{{ $i }}</div>
                                        <div class="tooth-body"></div>
                                        <!-- Treatment dropdown for each tooth -->
                                        <div class="tooth-dropdown">
                                            <select class="form-select form-select-sm treatment-select">
                                                <option value="">انتخاب درمان</option>
                                                <option value="1">ترمیم</option>
                                                <option value="2">اندو</option>
                                                <option value="3">پست</option>
                                                <option value="4">روکش</option>
                                                <option value="5">اندو، پست و روکش</option>
                                                <option value="7">ایمپلنت</option>
                                                <option value="6">کشیدن</option>
                                                <option value="8">جراحی نسج نرم</option>
                                                <option value="9">جراحی نسج سخت</option>
                                                <option value="10">CL</option>
                                            </select>
                                            <div class="tooth-dropdown-actions">
                                                <button type="button" class="btn btn-outline-danger btn-remove-treatment btn-lg" style="display: none; margin: 5px auto;">−</button>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <!-- دندان‌های چپ (1-8) -->
                            <div class="teeth-side left-side">
                                @for($i = 1; $i <= 8; $i++)
                                    @php $actualTooth = 40 + $i; @endphp
                                    <div class="tooth" data-tooth="{{ $actualTooth }}" data-jaw="lower" data-side="left" data-display="{{ $i }}">
                                        <div class="tooth-number">{{ $i }}</div>
                                        <div class="tooth-body"></div>
                                        <!-- Treatment dropdown for each tooth -->
                                        <div class="tooth-dropdown">
                                            <select class="form-select form-select-sm treatment-select">
                                                <option value="">انتخاب درمان</option>
                                                <option value="1">ترمیم</option>
                                                <option value="2">اندو</option>
                                                <option value="3">پست</option>
                                                <option value="4">روکش</option>
                                                <option value="5">اندو، پست و روکش</option>
                                                <option value="7">ایمپلنت</option>
                                                <option value="6">کشیدن</option>
                                                <option value="8">جراحی نسج نرم</option>
                                                <option value="9">جراحی نسج سخت</option>
                                                <option value="10">CL</option>
                                            </select>
                                            <div class="tooth-dropdown-actions">
                                                <button type="button" class="btn btn-outline-danger btn-remove-treatment btn-lg" style="display: none; margin: 5px auto;">−</button>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <div class="side-label left-label">راست</div>
                        </div>
                    </div>
                </div>

                <!-- لیست درمان‌های انتخاب شده -->
                <div class="mt-4" id="selected_treatments" style="display: none;">
                    <h6>درمان‌های انتخاب شده</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>دندان</th>
                                    <th>نوع درمان</th>
                                    <th>هزینه</th>
                                    <th>توضیحات</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody id="treatments_list">
                                <!-- درمان‌ها اینجا اضافه می‌شوند -->
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="2">مجموع</th>
                                    <th id="total_cost">0 ریال</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- دکمه‌های عمل -->
                <div class="mt-4 text-end" id="action_buttons" style="display: none;">
                    <button type="button" class="btn btn-primary btn-lg" onclick="saveTreatmentPlan()">
                        <i class="bi bi-check-lg me-1"></i>
                        ثبت طرح درمان
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* استایل چارت دندانی */
.dental-chart {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin: 20px 0;
}

.jaw-section h6 {
    color: #495057;
    font-weight: 600;
}

.teeth-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
}

.side-label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    padding: 10px 5px;
    background: rgba(108, 117, 125, 0.1);
    border-radius: 15px;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.right-label {
    order: 1;
}

.right-side {
    order: 2;
}

.left-side {
    order: 3;
}

.left-label {
    order: 4;
}

.teeth-side {
    display: flex;
    gap: 5px;
    position: relative;
}

.tooth {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 5px;
    border-radius: 8px;
    position: relative;
}

.tooth:hover {
    background: rgba(13, 110, 253, 0.1);
}

.tooth.selected {
    background: #0d6efd;
    color: white;
}

.tooth.has-treatment {
    background: #198754;
    color: white;
}

/* Treatment-specific colors */
.tooth.has-treatment-1 { /* ترمیم - Green */
    background: #198754;
    color: white;
}

.tooth.has-treatment-2 { /* اندو - Yellow */
    background: #ffc107;
    color: black;
}

.tooth.has-treatment-3 { /* پست - Blue */
    background: #0d6efd;
    color: white;
}

.tooth.has-treatment-4 { /* روکش - Blue */
    background: #0d6efd;
    color: white;
}

.tooth.has-treatment-5 { /* ایمپلنت - Gray */
    background: #6c757d;
    color: white;
}

.tooth.has-treatment-6 { /* ونیر کامپوزیت - Green */
    background: #198754;
    color: white;
}

/* Add new class for combination treatment (اندو، پست و روکش) - Orange */
.tooth.has-treatment-11 { /* اندو، پست و روکش - Orange */
    background: #fd7e14;
    color: white;
}

.tooth.has-treatment-7 { /* کشیدن - Red */
    background: #dc3545;
    color: white;
}

.tooth.has-treatment-8 { /* جراحی نسج نرم - Red */
    background: #dc3545;
    color: white;
}

.tooth.has-treatment-9 { /* جراحی نسج سخت - Red */
    background: #dc3545;
    color: white;
}

.tooth.has-treatment-10 { /* CL - Default color */
    background: #198754;
    color: white;
}

.tooth-number {
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 3px;
}

.tooth-body {
    width: 30px;
    height: 35px;
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 8px 8px 15px 15px;
    position: relative;
    transition: all 0.3s ease;
}

.tooth:hover .tooth-body {
    border-color: #0d6efd;
    box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
}

.tooth.selected .tooth-body {
    background: #fff;
    border-color: #fff;
}

.tooth.has-treatment .tooth-body {
    background: #fff;
    border-color: #fff;
}

/* Tooth dropdown styles */
.tooth-dropdown {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    padding: 10px;
    z-index: 1000;
    display: none;
    min-width: 180px;
    margin-top: 5px;
}

.tooth-dropdown-actions {
    display: flex;
    justify-content: center;
    margin-top: 5px;
}

.tooth-dropdown .btn {
    padding: 0.5rem 1rem; /* Larger buttons */
    font-size: 1rem; /* Larger font */
    min-width: 40px; /* Minimum width */
    min-height: 40px; /* Minimum height */
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Enhanced select dropdown styles */
.tooth-dropdown .form-select {
    padding: 8px 12px;
    font-size: 0.9rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: left 0.75rem center;
    background-size: 16px 12px;
    padding-left: 35px;
    transition: all 0.3s ease;
    cursor: pointer;
    width: 100%;
    margin-bottom: 8px;
}

.tooth-dropdown .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    outline: 0;
}

.tooth-dropdown .form-select:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
}

.tooth.selected .tooth-dropdown {
    display: block;
}

/* استایل فرم‌ها */
.position-relative .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    background-color: white;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endpush

@push('scripts')
<script>
let selectedPatientId = null;
let selectedTooth = null;
let treatments = [];
let totalCost = 0;
let lastAddedTreatment = null; // برای ذخیره آخرین درمان اضافه شده

// تابع نمایش پیام موفقیت آمیز
function showSuccessMessage(message) {
    // Generate a unique ID for this alert
    const alertId = 'success-alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert alert-success alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(alertHtml);
    
    // حذف خودکار بعد از 5 ثانیه
    setTimeout(function() {
        $('#' + alertId).fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
}

// Voice Recognition Functions
let recognition;
let isListening = false;
let continuousListening = false;

// Initialize speech recognition
function initSpeechRecognition() {
    try {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            console.error('Speech recognition not supported in this browser');
            $('#voiceStatus').html('<span class="text-danger">مرورگر شما از تشخیص صدا پشتیبانی نمی‌کند</span>');
            $('#startVoiceRecognition').prop('disabled', true);
            return false;
        }
        
        recognition = new SpeechRecognition();
        recognition.continuous = true;  // Enable continuous listening
        recognition.interimResults = true;  // Enable interim results for live display
        recognition.lang = 'fa-IR'; // Persian language
        
        recognition.onstart = function() {
            isListening = true;
            $('#voiceStatus').html('<span class="text-primary">در حال ضبط... لطفا صحبت کنید</span>');
            $('#startVoiceRecognition').html('<i class="bi bi-mic-fill me-2"></i> توقف ضبط');
            $('#startVoiceRecognition').removeClass('btn-primary').addClass('btn-danger');
        };
        
        recognition.onresult = function(event) {
            let interimTranscript = '';
            let finalTranscript = '';
            
            // Process all results
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                
                if (event.results[i].isFinal) {
                    finalTranscript += transcript;
                    
                    // Process the final voice command
                    processVoiceCommand(transcript);
                } else {
                    interimTranscript += transcript;
                }
            }
            
            // Display interim and final transcripts
            $('#voiceResult').show();
            $('#recognizedText').html(
                (finalTranscript ? '<strong>نهایی:</strong> ' + finalTranscript + '<br>' : '') + 
                (interimTranscript ? '<strong>در حال پردازش:</strong> ' + interimTranscript : '')
            );
            
            // Debug: Log the transcripts to console
            if (finalTranscript) {
                console.log('Final transcript:', finalTranscript);
            }
            if (interimTranscript) {
                console.log('Interim transcript:', interimTranscript);
            }
        };
        
        recognition.onerror = function(event) {
            console.error('Speech recognition error', event.error);
            $('#voiceStatus').html('<span class="text-danger">خطا در تشخیص صدا: ' + event.error + '</span>');
            resetVoiceButton();
        };
        
        recognition.onend = function() {
            isListening = false;
            $('#voiceStatus').html('<span class="text-muted">ضبط متوقف شد</span>');
            resetVoiceButton();
            
            // If continuous listening is enabled, restart recognition
            if (continuousListening) {
                startContinuousListening();
            }
        };
        
        return true;
    } catch (e) {
        console.error('Error initializing speech recognition:', e);
        $('#voiceStatus').html('<span class="text-danger">خطا در راه‌اندازی تشخیص صدا</span>');
        $('#startVoiceRecognition').prop('disabled', true);
        return false;
    }
}

// Reset voice button to initial state
function resetVoiceButton() {
    $('#startVoiceRecognition').html('<i class="bi bi-mic-fill me-2"></i> شروع ضبط صدا');
    $('#startVoiceRecognition').removeClass('btn-danger').addClass('btn-primary');
}

// Toggle voice recognition
function toggleVoiceRecognition() {
    if (!recognition) {
        if (!initSpeechRecognition()) {
            return;
        }
    }
    
    if (isListening) {
        continuousListening = false;
        recognition.stop();
    } else {
        continuousListening = true;
        try {
            recognition.start();
        } catch (e) {
            console.error('Error starting recognition:', e);
            $('#voiceStatus').html('<span class="text-danger">خطا در شروع ضبط: ' + e.message + '</span>');
        }
    }
}

// Start continuous listening
function startContinuousListening() {
    if (recognition && !isListening) {
        try {
            recognition.start();
        } catch (e) {
            console.error('Error starting continuous recognition:', e);
        }
    }
}

// Process voice command and add treatment
function processVoiceCommand(command) {
    // Ignore empty or very short commands
    if (!command || command.trim().length < 2) {
        return;
    }
    
    console.log('Processing voice command:', command);

    // Function to convert Persian numbers and words to English numbers
    function convertPersianNumbers(command) {
        // Convert Persian numbers to English for processing
        const persianToEnglish = {
            '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
            '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9'
        };
        
        // Convert Persian number words to numerals
        const persianWordsToNumbers = {
            'صفر': '0', 'یک': '1', 'دو': '2', 'سه': '3', 'چهار': '4',
            'پنج': '5', 'شیش': '6', 'شش': '6', 'هفت': '7', 'هشت': '8', 'نه': '9',
            '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
            '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9'
        };
        
        let processedCommand = command;
        
        // First convert Persian number words to numerals (with more aggressive matching)
        for (const [word, numeral] of Object.entries(persianWordsToNumbers)) {
            // Use a more flexible regex that handles different forms with word boundaries
            const regex = new RegExp('\\b' + word + '\\b', 'g');
            processedCommand = processedCommand.replace(regex, numeral);
        }
        
        // Then convert any remaining Persian numerals to English numerals
        for (const [persian, english] of Object.entries(persianToEnglish)) {
            processedCommand = processedCommand.replace(new RegExp(persian, 'g'), english);
        }
        
        console.log('Converted command:', processedCommand);
        return processedCommand;
    }
    
    let processedCommand = convertPersianNumbers(command);
    console.log('Processed command:', processedCommand);

    // Parse the command
    // Example: "6 بالا راست ترمیم" or "دندان 3 پایین چپ کشیدن"
    const toothRegex = /(\d+)\s*(?:دندان\s*)?(بالا|پایین)\s*(راست|چپ)/i;
    const toothMatch = processedCommand.match(toothRegex);
    
    if (!toothMatch) {
        console.log('No tooth match found in command');
        // Don't show error for unrecognized commands in continuous mode
        return;
    }
    
    const toothNumber = parseInt(toothMatch[1]);
    const jaw = toothMatch[2]; // بالا یا پایین
    const side = toothMatch[3]; // راست یا چپ
    

    
    // Validate tooth number (1-8 for each jaw/side)
    if (toothNumber < 1 || toothNumber > 8) {
        console.log('Invalid tooth number');
        return;
    }
    
    // Map tooth position to actual tooth number based on user's expected system
    // The user expects teeth to be numbered 1-8 from center outward in each quadrant
    // 
    // For all quadrants:
    // Tooth 1 = closest to center (midline)
    // Tooth 8 = furthest from center
    // 
    // Visual arrangement due to CSS Flexbox ordering:
    // Right label | Right teeth | Left teeth | Left label
    // 
    // Within each side, teeth are ordered left to right in HTML:
    // Right side: tooth 1 (leftmost) to tooth 8 (rightmost)
    // Left side: tooth 1 (leftmost) to tooth 8 (rightmost)
    // 
    // So when user says "8 پایین راست":
    // - jaw = "پایین", side = "راست", toothNumber = 8
    // - This should map to data-tooth="38" (rightmost tooth in lower right quadrant)
    
    // Map tooth position to actual tooth number based on user's expected system
    // The user expects teeth to be numbered 1-8 from center outward in each quadrant
    // 
    // For all quadrants:
    // Tooth 1 = closest to center (midline)
    // Tooth 8 = furthest from center
    // 
    // Visual arrangement due to CSS Flexbox ordering:
    // Right label | Right teeth | Left teeth | Left label
    // 
    // Within each side, teeth are ordered left to right in HTML:
    // Right side: tooth 1 (leftmost) to tooth 8 (rightmost)
    // Left side: tooth 1 (leftmost) to tooth 8 (rightmost)
    // 
    // So when user says "8 پایین راست":
    // - jaw = "پایین", side = "راست", toothNumber = 8
    // - This should map to data-tooth="31" (rightmost tooth in lower right quadrant)
    
    // Map tooth position to actual tooth number based on user's expected system
    // The user expects a reverse numbering system:
    // Tooth 1 = furthest from center (rightmost visually)
    // Tooth 8 = closest to center (leftmost visually)
    // 
    // For each quadrant, we reverse the numbering:
    // actualToothNumber = (range start + range size) - toothNumber
    
    // Map tooth position to actual tooth number based on the correct international to Iranian system
    // Upper right quadrant (11-18): 1 is closest to center, 8 is furthest
    // Upper left quadrant (21-28): 1 is closest to center, 8 is furthest
    // Lower left quadrant (31-38): 1 is closest to center, 8 is furthest 
    // Lower right quadrant (41-48): 1 is closest to center, 8 is furthest
    
    // Note: Due to the HTML structure and visual layout, we need to account for how
    // the sides are labeled and how the teeth are actually arranged
    
    let actualToothNumber;
    if (jaw === 'بالا') {
        if (side === 'راست') {
            // Upper right: data-tooth values 11-18
            // toothNumber 1 = data-tooth 11 (closest to center)
            // toothNumber 8 = data-tooth 18 (furthest from center)
            actualToothNumber = 10 + toothNumber;
        } else {
            // Upper left: data-tooth values 21-28
            // toothNumber 1 = data-tooth 21 (closest to center)
            // toothNumber 8 = data-tooth 28 (furthest from center)
            actualToothNumber = 20 + toothNumber;
        }
    } else { // پایین
        if (side === 'راست') {
            // Lower right: data-tooth values 41-48
            // toothNumber 1 = data-tooth 41 (closest to center)
            // toothNumber 8 = data-tooth 48 (furthest from center)
            actualToothNumber = 40 + toothNumber;
        } else {
            // Lower left: data-tooth values 31-38
            // toothNumber 1 = data-tooth 31 (closest to center)
            // toothNumber 8 = data-tooth 38 (furthest from center)
            actualToothNumber = 30 + toothNumber;
        }
    }
    

    
    // Extract treatment type
    const treatmentTypes = {
        'ترمیم': '1',
        'اندو': '2',
        'پست': '3',
        'روکش': '4',
        'اندو، پست و روکش': '5',
        'کشیدن': '6',
        'ایمپلنت': '7',
        'جراحی نسج نرم': '8',
        'جراحی نسج سخت': '9',
        'cl': '10',
        'CL': '10'
    };
    
    let treatmentType = null;
    let treatmentText = '';
    
    // Find the best matching treatment type
    for (const [key, value] of Object.entries(treatmentTypes)) {
        if (processedCommand.includes(key)) {
            treatmentType = value;
            treatmentText = key;
            break;
        }
    }
    
    // If no exact match, try partial matching
    if (!treatmentType) {
        for (const [key, value] of Object.entries(treatmentTypes)) {
            if (processedCommand.toLowerCase().includes(key.toLowerCase())) {
                treatmentType = value;
                treatmentText = key;
                break;
            }
        }
    }
    
    if (!treatmentType) {
        $('#voiceStatus').html('<span class="text-warning">درمان شناسایی نشد. لطفاً دوباره تلاش کنید.</span>');
        return;
    }
    
    // Find the tooth element
    const toothElement = $(`.tooth[data-tooth="${actualToothNumber}"]`);
    
    if (toothElement.length === 0) {
        $('#voiceStatus').html('<span class="text-warning">دندان مورد نظر یافت نشد.</span>');
        return;
    }
    
    // Check if patient is selected
    if (!selectedPatientId) {
        $('#voiceStatus').html('<span class="text-warning">لطفاً ابتدا بیمار را انتخاب کنید.</span>');
        return;
    }
    
    // Check if this treatment already exists for this tooth to prevent duplicates
    const existingTreatmentIndex = treatments.findIndex(t => t.tooth === actualToothNumber && t.treatmentType === treatmentType);
    if (existingTreatmentIndex !== -1) {
        $('#voiceStatus').html('<span class="text-info">این درمان قبلاً برای این دندان ثبت شده است.</span>');
        return;
    }
    
    // Create treatment object
    const displayNumber = toothElement.data('display');
    const jawText = jaw === 'بالا' ? 'بالا' : 'پایین';
    const sideText = side === 'راست' ? 'چپ' : 'راست';
    const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    const toothDescription = `دندان ${persianDigits[displayNumber]} ${jawText} ${sideText}`;
    
    const newTreatment = {
        tooth: actualToothNumber,
        toothDescription: toothDescription,
        treatmentType: treatmentType,
        treatmentText: treatmentText,
        cost: 0,
        notes: ''
    };
    

    
    // Add to treatments array using the same logic as the dropdown change handler
    // Check if this is a combination treatment (5)
    if (treatmentType == '5') {
        // Remove any existing individual treatments for this tooth
        treatments = treatments.filter(t => t.tooth !== actualToothNumber);
        
        // Add the combination treatment
        treatments.push(newTreatment);
    } else {
        // Check if this treatment already exists for this tooth
        const existingTreatmentIndex = treatments.findIndex(t => t.tooth === actualToothNumber && t.treatmentType == treatmentType);
        
        if (existingTreatmentIndex === -1) {
            // Remove any existing combination treatment for this tooth
            treatments = treatments.filter(t => !(t.tooth === actualToothNumber && t.treatmentType == '5'));
            
            // Add new treatment
            treatments.push(newTreatment);
        }
    }
    
    // Mark tooth as having treatment with specific color class
    toothElement.addClass('has-treatment');
    toothElement.removeClass('has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11');
    
    // Apply color based on the treatment type
    const treatmentClass = getTreatmentColorClass(treatmentType);
    toothElement.addClass(treatmentClass);
    
    // Update treatments list
    updateTreatmentsList();
    
    // Show success message
    const successMessage = `درمان "${treatmentText}" برای ${toothDescription} با موفقیت اضافه شد`;
    $('#voiceStatus').html('<span class="text-success">' + successMessage + '</span>');
    
    // Show success notification
    showSuccessMessage(successMessage);
    

    
    // Make sure the treatment list is visible
    $('#selected_treatments').show();
    $('#action_buttons').show();
}

$(document).ready(function() {
    // Add event listener to voice button
    $('#startVoiceRecognition').on('click', toggleVoiceRecognition);
    
    // Auto-focus on search field when page loads
    $('#patient_search').focus();
    
    // Handle Enter key in search field
    $('#patient_search').keypress(function(e) {
        if (e.which === 13) { // Enter key
            // If the create button is visible, click it
            if ($('#create_patient_btn').is(':visible')) {
                $('#create_patient_btn').click();
            }
        }
    });
    
    // Handle Enter key in phone input field
    $('#patient_phone_input').keypress(function(e) {
        if (e.which === 13) { // Enter key
            createPatientWithPhone();
        }
    });
    
    // Handle Persian/English digit conversion for phone input
    $('#patient_phone_input').on('input', function() {
        let value = $(this).val();
        
        // Define digit mappings
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        // Convert any Persian digits to English for processing
        for (let i = 0; i < 10; i++) {
            value = value.replace(new RegExp(persianDigits[i], 'g'), englishDigits[i]);
        }
        
        // Ensure it starts with 09
        if (value.length >= 1 && value[0] !== '0') {
            value = '0' + value;
        }
        if (value.length >= 2 && value[1] !== '9') {
            value = '09' + value.substring(2);
        }
        
        // Limit to 11 digits
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        
        // Convert back to Persian digits for display
        let displayValue = value;
        for (let i = 0; i < 10; i++) {
            displayValue = displayValue.replace(new RegExp(englishDigits[i], 'g'), persianDigits[i]);
        }
        
        // Only update the field if the value has changed to avoid cursor issues
        if ($(this).val() !== displayValue) {
            $(this).val(displayValue);
        }
    });
    
    // Ensure numeric keyboard on mobile devices
    $('#patient_phone_input').on('focus', function() {
        $(this).attr('inputmode', 'numeric');
        $(this).attr('pattern', '[0-9]*');
    });
    
    // Patient search functionality
    let searchTimeout;
    $('#patient_search').on('input', function() {
        const query = $(this).val();
        const dropdown = $('#patient_dropdown');
        const createBtn = $('#create_patient_btn');
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            dropdown.hide();
            createBtn.hide();
            return;
        }
        
        // Show create button when there's text in the search field
        createBtn.show();
        
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '/api/search-patients',
                method: 'GET',
                data: { q: query },
                success: function(response) {
                    dropdown.empty();
                    
                    if (response.length === 0) {
                        dropdown.append('<div class="dropdown-item-text text-muted">هیچ بیماری یافت نشد. برای ایجاد بیمار جدید روی دکمه "ایجاد بیمار" کلیک کنید.</div>');
                    } else {
                        response.forEach(function(patient) {
                            // Convert English digits to Persian digits for display in search results
                            let displayPhone = patient.phone || '';
                            if (displayPhone) {
                                const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                                const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                                for (let i = 0; i < 10; i++) {
                                    displayPhone = displayPhone.replace(new RegExp(englishDigits[i], 'g'), persianDigits[i]);
                                }
                            }
                            
                            const item = $(`
                                <a href="#" class="dropdown-item patient-item" data-id="${patient.id}" 
                                   data-name="${patient.full_name}" data-phone="${patient.phone}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>${patient.full_name}</strong>
                                            <br><small class="text-muted">${displayPhone}</small>
                                        </div>
                                        <small class="text-muted">${patient.national_code || ''}</small>
                                    </div>
                                </a>
                            `);
                            dropdown.append(item);
                        });
                    }
                    
                    dropdown.show();
                },
                error: function() {
                    dropdown.empty().append('<div class="dropdown-item-text text-danger">خطا در جستجو</div>');
                    dropdown.show();
                }
            });
        }, 300);
    });
    
    // Handle create patient button click - Show phone input form
    $('#create_patient_btn').on('click', function() {
        const query = $('#patient_search').val().trim();
        if (query.length < 2) {
            alert('لطفا نام یا شماره تماس بیمار را وارد کنید');
            return;
        }
        
        // Check if the query is already a phone number
        const phoneRegex = /^09\d{9}$/;
        if (phoneRegex.test(query)) {
            // If it's already a valid phone number, create patient directly
            createNewPatientDirectlyWithPhone(query);
        } else {
            // Parse the query to extract first name and last name
            const parts = query.split(' ');
            let firstName = '';
            let lastName = '';
            
            if (parts.length === 1) {
                // If only one word, treat it as last name
                lastName = parts[0];
            } else if (parts.length >= 2) {
                // If two or more words, first word is first name, rest are last name
                firstName = parts[0];
                lastName = parts.slice(1).join(' ');
            }
            
            // Store the parsed names in global variables
            window.tempFirstName = firstName;
            window.tempLastName = lastName;
            
            // Hide dropdown and show phone input form
            $('#patient_dropdown').hide();
            $('#create_patient_btn').hide();
            $('#phone_input_form').show();
            $('#patient_phone_input').focus();
        }
    });
    
    // Patient selection
    $(document).on('click', '.patient-item', function(e) {
        e.preventDefault();
        
        selectedPatientId = $(this).data('id');
        const patientName = $(this).data('name');
        let patientPhone = $(this).data('phone');
        
        $('#patient_search').val('');
        $('#patient_dropdown').hide();
        $('#create_patient_btn').hide();
        
        $('#patient_name').text(patientName);
        
        // Convert English digits to Persian digits for display
        if (patientPhone) {
            const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            for (let i = 0; i < 10; i++) {
                patientPhone = patientPhone.replace(new RegExp(englishDigits[i], 'g'), persianDigits[i]);
            }
        }
        
        $('#patient_phone').text(patientPhone || '');
        $('#patient_initial').text(patientName.charAt(0));
        $('#selected_patient').show();
        
        // بارگیری طرح درمان قبلی اگر وجود دارد
        loadExistingTreatments(selectedPatientId);
    });
    
    // Close dropdown when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('#patient_dropdown').hide();
            $('#create_patient_btn').hide();
        }
    });
    
    // Tooth selection - استفاده از event delegation برای اطمینان از کارکرد
    $(document).on('click', '.tooth', function(e) {
        if (!selectedPatientId) {
            alert('لطفا ابتدا بیمار را انتخاب کنید');
            return;
        }
        
        // Prevent closing dropdown when clicking inside it
        if ($(e.target).closest('.tooth-dropdown').length) {
            return;
        }
        
        // Close all other tooth dropdowns
        $('.tooth').not(this).removeClass('selected');
        
        // Toggle selection for clicked tooth
        $(this).toggleClass('selected');
        
        selectedTooth = $(this).data('tooth');
        const displayNumber = $(this).data('display');
        const jaw = $(this).data('jaw');
        const side = $(this).data('side');
        
        // تبدیل اطلاعات به فارسی
        const jawText = jaw === 'upper' ? 'بالا' : 'پایین';
        const sideText = side === 'right' ? 'چپ' : 'راست';
        const persianNumber = displayNumber.toString().replace(/\d/g, function(match) {
            const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            return persianDigits[parseInt(match)];
        });
        const toothDescription = `دندان ${persianNumber} ${jawText} ${sideText}`;
        
        // Show/hide action buttons based on selection and whether tooth has treatment
        if ($(this).hasClass('selected')) {
            if ($(this).hasClass('has-treatment')) {
                // Tooth already has treatment, show remove button
                $(this).find('.btn-remove-treatment').show();
            } else {
                // Tooth doesn't have treatment, hide remove button
                $(this).find('.btn-remove-treatment').hide();
            }
            // Always show the treatment selection dropdown when tooth is selected
            // The dropdown will be shown by the CSS rule: .tooth.selected .tooth-dropdown { display: block; }
        } else {
            // When deselecting, hide the remove button
            $(this).find('.btn-remove-treatment').hide();
        }
        
        // Update treatments list visibility
        if (treatments.length > 0) {
            $('#selected_treatments').show();
            $('#action_buttons').show();
        } else {
            $('#selected_treatments').hide();
            $('#action_buttons').hide();
        }
    });
    
    // Handle remove treatment button click
    $(document).on('click', '.btn-remove-treatment', function(e) {
        e.stopPropagation(); // Prevent the tooth click event from firing
        
        const toothElement = $(this).closest('.tooth');
        const tooth = toothElement.data('tooth');
        
        // Remove treatments for this tooth
        treatments = treatments.filter(t => t.tooth !== tooth);
        
        // Remove treatment classes from tooth
        toothElement.removeClass('has-treatment has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11');
        
        // Hide the remove button
        $(this).hide();
        
        // Update the treatments list
        updateTreatmentsList();
        
        // Hide the dropdown
        toothElement.removeClass('selected');
    });
    
    // Handle treatment selection from tooth dropdown - add treatment to tooth
    $(document).on('change', '.treatment-select', function() {
        const toothElement = $(this).closest('.tooth');
        const treatmentType = $(this).val();
        const treatmentText = $(this).find('option:selected').text();
        
        if (!treatmentType) {
            return;
        }
        
        const tooth = toothElement.data('tooth');
        const displayNumber = toothElement.data('display');
        const jaw = toothElement.data('jaw');
        const side = toothElement.data('side');
        
        const jawText = jaw === 'upper' ? 'بالا' : 'پایین';
        const sideText = side === 'right' ? 'چپ' : 'راست';
        const persianNumber = displayNumber.toString().replace(/\d/g, function(match) {
            const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            return persianDigits[parseInt(match)];
        });
        const toothDescription = `دندان ${persianNumber} ${jawText} ${sideText}`;
        
        // ذخیره اطلاعات درمان قبل از اضافه کردن برای قابلیت Undo
        const newTreatment = {
            tooth: tooth,
            toothDescription: toothDescription,
            treatmentType: treatmentType,
            treatmentText: treatmentText,
            cost: 0, // Default cost
            notes: ''
        };
        
        // ذخیره آخرین درمان اضافه شده برای قابلیت Undo
        lastAddedTreatment = {...newTreatment};
        
        // نمایش دکمه Undo
        $('#undo_treatment_btn').show();
        
        // پنهان کردن دکمه Undo بعد از 10 ثانیه
        setTimeout(function() {
            $('#undo_treatment_btn').hide();
            lastAddedTreatment = null;
        }, 10000);
        
        // Special handling for combination treatment (5)
        if (treatmentType == '5') {
            // Remove any existing individual treatments for this tooth
            treatments = treatments.filter(t => t.tooth !== tooth);
            
            // Add the combination treatment
            treatments.push(newTreatment);
        } else {
            // Check if this treatment already exists for this tooth
            const existingTreatmentIndex = treatments.findIndex(t => t.tooth === tooth && t.treatmentType == treatmentType);
            
            if (existingTreatmentIndex === -1) {
                // Remove any existing combination treatment for this tooth
                treatments = treatments.filter(t => !(t.tooth === tooth && t.treatmentType == '5'));
                
                // Add new treatment
                treatments.push(newTreatment);
            }
        }
        
        // Mark tooth as having treatment with specific color class
        toothElement.addClass('has-treatment');
        toothElement.removeClass('has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11');
        
        // Apply color based on the treatment type
        const treatmentClass = getTreatmentColorClass(treatmentType);
        toothElement.addClass(treatmentClass);
        
        toothElement.removeClass('selected');
        toothElement.find('.btn-remove-treatment').hide();
        $(this).val(''); // Reset dropdown
        
        updateTreatmentsList();
        
        // Make sure the treatment list is visible
        $('#selected_treatments').show();
        $('#action_buttons').show();
    });
    
    // Function to get the appropriate color class for a treatment type
    function getTreatmentColorClass(treatmentType) {
        // Convert to string if it's a number
        treatmentType = treatmentType.toString();
        
        switch(treatmentType) {
            case '1': // ترمیم
                return 'has-treatment-1';
            case '2': // اندو
                return 'has-treatment-2';
            case '3': // پست
                return 'has-treatment-3';
            case '4': // روکش
                return 'has-treatment-3';
            case '5': // اندو، پست و روکش
                return 'has-treatment-11';
            case '7': // ایمپلنت
                return 'has-treatment-5';
            case '6': // کشیدن
                return 'has-treatment-7';
            case '8': // جراحی نسج نرم
                return 'has-treatment-7';
            case '9': // جراحی نسج سخت
                return 'has-treatment-7';
            case '10': // CL
                return 'has-treatment-7';
            default:
                return 'has-treatment';
        }
    }
    
    // Update the removeToothTreatments function to handle color classes
    function removeToothTreatments(tooth) {
        treatments = treatments.filter(t => t.tooth !== tooth);
        $(`.tooth[data-tooth="${tooth}"]`).removeClass('has-treatment has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11');
        updateTreatmentsList();
    }
    
    // Update the editToothTreatments function to handle color classes
    function editToothTreatments(tooth) {
        // Find the tooth element
        const toothElement = $(`.tooth[data-tooth="${tooth}"]`);
        
        // Select the tooth for editing
        $('.tooth').removeClass('selected');
        toothElement.addClass('selected');
        selectedTooth = tooth;
        
        // Show tooth description
        const toothData = treatments.find(t => t.tooth === tooth);
        if (toothData) {
            $('#selected_tooth_number').text(toothData.toothDescription);
        }
        
        // Remove all treatments for this tooth from the list (for re-editing)
        treatments = treatments.filter(t => t.tooth !== tooth);
        toothElement.removeClass('has-treatment has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10');
        updateTreatmentsList();
    }
    
    // Update the updateTreatmentsList function to apply color classes when treatments change
    function updateTreatmentsList() {
        const tbody = $('#treatments_list');
        tbody.empty();
        
        totalCost = 0;
        
        // Group treatments by tooth
        const treatmentsByTooth = {};
        treatments.forEach(function(treatment) {
            totalCost += treatment.cost;
            
            if (!treatmentsByTooth[treatment.tooth]) {
                treatmentsByTooth[treatment.tooth] = {
                    tooth: treatment.tooth,
                    toothDescription: treatment.toothDescription,
                    treatments: []
                };
            }
            treatmentsByTooth[treatment.tooth].treatments.push(treatment);
        });
        
        // Apply color classes to teeth
        $('.tooth').removeClass('has-treatment has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11');
        
        // Display grouped treatments and apply color classes
        Object.values(treatmentsByTooth).forEach(function(toothData) {
            // Check if any treatment is the combination treatment (5)
            const hasCombinationTreatment = toothData.treatments.some(t => t.treatmentType == '5');
            
            // If there's a combination treatment, use its text; otherwise join all treatments
            let treatmentTexts;
            if (hasCombinationTreatment) {
                treatmentTexts = toothData.treatments.find(t => t.treatmentType == '5').treatmentText;
            } else {
                treatmentTexts = toothData.treatments.map(t => t.treatmentText).join(' + ');
            }
            
            const totalToothCost = toothData.treatments.reduce((sum, t) => sum + t.cost, 0);
            const notes = toothData.treatments.map(t => t.notes).filter(n => n).join(', ') || '-';
            
            // Apply color class to tooth based on treatment type
            if (toothData.treatments.length > 0) {
                const toothElement = $(`.tooth[data-tooth="${toothData.tooth}"]`);
                toothElement.addClass('has-treatment');
                
                // If there's a combination treatment, use its color; otherwise use the first treatment's color
                let treatmentClass;
                if (hasCombinationTreatment) {
                    treatmentClass = getTreatmentColorClass('5');
                } else {
                    treatmentClass = getTreatmentColorClass(toothData.treatments[0].treatmentType);
                }
                toothElement.addClass(treatmentClass);
            }
            
            const row = `
                <tr>
                    <td>${toothData.toothDescription}</td>
                    <td>${treatmentTexts}</td>
                    <td>${totalToothCost.toLocaleString()} ریال</td>
                    <td>${notes}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                onclick="editToothTreatments(${toothData.tooth})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="removeToothTreatments(${toothData.tooth})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
        
        $('#total_cost').text(totalCost.toLocaleString() + ' ریال');
        
        // Always show the treatment list when there are treatments
        if (treatments.length > 0) {
            $('#selected_treatments').show();
            $('#action_buttons').show();
        } else {
            $('#selected_treatments').hide();
            $('#action_buttons').hide();
        }
    }
});

function clearPatientSelection() {
    selectedPatientId = null;
    $('#selected_patient').hide();
    $('#patient_search').val('');
    
    // پاک کردن طرح درمان قبلی
    clearExistingTreatments();
    
    // ریست کردن چارت دندان
    resetDentalChart();
}

// تابع بارگیری طرح درمان قبلی
function loadExistingTreatments(patientId) {
    $.ajax({
        url: '/api/get-patient-treatments',
        method: 'POST',
        data: { 
            patient_id: patientId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success && response.has_treatment) {
                // نمایش پیام اطلاع به کاربر
                showInfoMessage('طرح درمان قبلی بارگیری شد - قابل ویرایش است');
                
                // بارگیری درمان‌ها
                treatments = response.treatments;
                totalCost = response.total_cost;
                
                // علامت گذاری دندان‌ها به عنوان دارای درمان
                treatments.forEach(function(treatment) {
                    $(`.tooth[data-tooth="${treatment.tooth}"]`).addClass('has-treatment');
                });
                
                // به روزرسانی لیست درمان‌ها
                updateTreatmentsList();
            }
            
            // Update patient phone display with Persian digits
            if (response.patient && response.patient.phone) {
                const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                let displayPhone = response.patient.phone;
                for (let i = 0; i < 10; i++) {
                    displayPhone = displayPhone.replace(new RegExp(englishDigits[i], 'g'), persianDigits[i]);
                }
                $('#patient_phone').text(displayPhone);
            }
        },
        error: function(xhr) {
            console.log('خطا در بارگیری طرح درمان:', xhr);
        }
    });
}

// پاک کردن طرح درمان قبلی
function clearExistingTreatments() {
    treatments = [];
    totalCost = 0;
    $('.tooth').removeClass('has-treatment selected');
    $('#selected_treatments').hide();
    $('#action_buttons').hide();
    $('#treatment_selection').hide();
    $('#undo_remove_btn').hide(); // پنهان کردن دکمه Undo
    lastRemovedTreatment = null; // پاک کردن آخرین درمان حذف شده
}

// تابع نمایش پیام اطلاعات
function showInfoMessage(message) {
    // Generate a unique ID for this alert
    const alertId = 'info-alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert alert-info alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(alertHtml);
    
    // حذف خودکار بعد از 4 ثانیه
    setTimeout(function() {
        $('#' + alertId).fadeOut(500, function() {
            $(this).remove();
        });
    }, 4000);
}

function clearNewPatientForm() {
    $('#new_first_name, #new_last_name, #new_phone').val('');
    $('#new_first_name').focus();
}

function cancelPhoneInput() {
    $('#phone_input_form').hide();
    $('#patient_search').val('');
    $('#create_patient_btn').hide();
    $('#patient_dropdown').hide();
    $('#patient_search').focus();
}

function createPatientWithPhone() {
    let phone = $('#patient_phone_input').val().trim();
    
    // Convert Persian digits to English digits for database storage
    const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    for (let i = 0; i < 10; i++) {
        phone = phone.replace(new RegExp(persianDigits[i], 'g'), englishDigits[i]);
    }
    
    // Validate phone number format
    const phoneRegex = /^09\d{9}$/;
    if (!phoneRegex.test(phone)) {
        alert('لطفا شماره تماس را به فرم صحیح وارد کنید (09123456789)');
        $('#patient_phone_input').focus();
        return;
    }
    
    const data = {
        first_name: window.tempFirstName || '',
        last_name: window.tempLastName || '',
        phone: phone,
        _token: '{{ csrf_token() }}'
    };
    
    // Show loading state on the create button
    const submitBtn = $('.btn-primary[onclick="createPatientWithPhone()"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="bi bi-hourglass-split me-1"></i> در حال ایجاد...');
    submitBtn.prop('disabled', true);
    
    $.ajax({
        url: '/api/quick-create-patient',
        method: 'POST',
        data: data,
        success: function(response) {
            // انتخاب بیمار جدید
            selectedPatientId = response.patient.id;
            
            $('#patient_name').text(response.patient.full_name);
            
            // Convert English digits to Persian digits for display
            let displayPhone = response.patient.phone;
            for (let i = 0; i < 10; i++) {
                displayPhone = displayPhone.replace(new RegExp(englishDigits[i], 'g'), persianDigits[i]);
            }
            
            $('#patient_phone').text(displayPhone);
            $('#patient_initial').text(response.patient.full_name.charAt(0));
            $('#selected_patient').show();
            
            // Clear search field and hide phone input form
            $('#patient_search').val('');
            $('#phone_input_form').hide();
            
            showSuccessMessage('بیمار جدید با موفقیت ایجاد و انتخاب شد');
            
            // برای بیمار جدید نیازی به بارگیری طرح درمان قبلی نیست
        },
        error: function(xhr) {
            let errorMessage = 'خطا در ایجاد بیمار';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join('\n');
            }
            
            alert(errorMessage);
        },
        complete: function() {
            // Reset button state
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }
    });
}

// Function to create a new patient directly without showing a form
function createNewPatientDirectly(firstName, lastName) {
    // Generate a random phone number since we don't have one
    // In a real application, you might want to prompt for this or use a default
    const randomPhone = '09' + Math.floor(Math.random() * 100000000).toString().padStart(9, '0');
    
    const data = {
        first_name: firstName,
        last_name: lastName,
        phone: randomPhone,
        _token: '{{ csrf_token() }}'
    };
    
    // Show loading state on the create button
    const createBtn = $('#create_patient_btn');
    const originalText = createBtn.html();
    createBtn.html('<i class="bi bi-hourglass-split me-1"></i> در حال ایجاد...');
    createBtn.prop('disabled', true);
    
    $.ajax({
        url: '/api/quick-create-patient',
        method: 'POST',
        data: data,
        success: function(response) {
            // انتخاب بیمار جدید
            selectedPatientId = response.patient.id;
            
            $('#patient_name').text(response.patient.full_name);
            
            // Convert English digits to Persian digits for display
            const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            let displayPhone = response.patient.phone;
            for (let i = 0; i < 10; i++) {
                displayPhone = displayPhone.replace(new RegExp(englishDigits[i], 'g'), persianDigits[i]);
            }
            
            $('#patient_phone').text(displayPhone);
            $('#patient_initial').text(response.patient.full_name.charAt(0));
            $('#selected_patient').show();
            
            // Clear search field
            $('#patient_search').val('');
            
            showSuccessMessage('بیمار جدید با موفقیت ایجاد و انتخاب شد');
            
            // برای بیمار جدید نیازی به بارگیری طرح درمان قبلی نیست
        },
        error: function(xhr) {
            let errorMessage = 'خطا در ایجاد بیمار';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join('\n');
            }
            
            alert(errorMessage);
        },
        complete: function() {
            // Reset button state
            createBtn.html(originalText);
            createBtn.prop('disabled', false);
        }
    });
}

// Function to create a new patient with phone number
function createNewPatientDirectlyWithPhone(phone) {
    // Convert Persian digits to English digits for database storage
    const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    for (let i = 0; i < 10; i++) {
        phone = phone.replace(new RegExp(persianDigits[i], 'g'), englishDigits[i]);
    }
    
    const data = {
        first_name: '',
        last_name: '',
        phone: phone,
        _token: '{{ csrf_token() }}'
    };
    
    // Show loading state on the create button
    const createBtn = $('#create_patient_btn');
    const originalText = createBtn.html();
    createBtn.html('<i class="bi bi-hourglass-split me-1"></i> در حال ایجاد...');
    createBtn.prop('disabled', true);
    
    $.ajax({
        url: '/api/quick-create-patient',
        method: 'POST',
        data: data,
        success: function(response) {
            // انتخاب بیمار جدید
            selectedPatientId = response.patient.id;
            
            $('#patient_name').text(response.patient.full_name);
            
            // Convert English digits to Persian digits for display
            let displayPhone = response.patient.phone;
            for (let i = 0; i < 10; i++) {
                displayPhone = displayPhone.replace(new RegExp(englishDigits[i], 'g'), persianDigits[i]);
            }
            
            $('#patient_phone').text(displayPhone);
            $('#patient_initial').text(response.patient.full_name.charAt(0));
            $('#selected_patient').show();
            
            // Clear search field
            $('#patient_search').val('');
            
            showSuccessMessage('بیمار جدید با موفقیت ایجاد و انتخاب شد');
            
            // برای بیمار جدید نیازی به بارگیری طرح درمان قبلی نیست
        },
        error: function(xhr) {
            let errorMessage = 'خطا در ایجاد بیمار';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join('\n');
            }
            
            alert(errorMessage);
        },
        complete: function() {
            // Reset button state
            createBtn.html(originalText);
            createBtn.prop('disabled', false);
        }
    });
}

function createNewPatient() {
    const firstName = $('#new_first_name').val().trim();
    const lastName = $('#new_last_name').val().trim();
    let phone = $('#new_phone').val().trim();
    
    // Convert Persian digits to English digits for database storage
    const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    for (let i = 0; i < 10; i++) {
        phone = phone.replace(new RegExp(persianDigits[i], 'g'), englishDigits[i]);
    }
    
    // اعتبارسنجی ساده
    if (!firstName) {
        alert('لطفا نام را وارد کنید');
        $('#new_first_name').focus();
        return;
    }
    
    if (!lastName) {
        alert('لطفا نام خانوادگی را وارد کنید');
        $('#new_last_name').focus();
        return;
    }
    
    if (!phone) {
        alert('لطفا شماره تماس را وارد کنید');
        $('#new_phone').focus();
        return;
    }
    
    // بررسی فرمت شماره تماس
    const phoneRegex = /^09\d{9}$/;
    if (!phoneRegex.test(phone)) {
        alert('لطفا شماره تماس را به فرم صحیح وارد کنید (09123456789)');
        $('#new_phone').focus();
        return;
    }
    
    const data = {
        first_name: firstName,
        last_name: lastName,
        phone: phone,
        _token: '{{ csrf_token() }}'
    };
    
    // نمایش حالت loading
    const submitBtn = $('.btn-primary[onclick="createNewPatient()"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="bi bi-hourglass-split me-1"></i> در حال ایجاد...');
    submitBtn.prop('disabled', true);
    
    $.ajax({
        url: '/api/quick-create-patient',
        method: 'POST',
        data: data,
        success: function(response) {
            // انتخاب بیمار جدید
            selectedPatientId = response.patient.id;
            
            $('#patient_name').text(response.patient.full_name);
            
            // Convert English digits to Persian digits for display
            let displayPhone = response.patient.phone;
            for (let i = 0; i < 10; i++) {
                displayPhone = displayPhone.replace(new RegExp(englishDigits[i], 'g'), persianDigits[i]);
            }
            
            $('#patient_phone').text(displayPhone);
            $('#patient_initial').text(response.patient.full_name.charAt(0));
            $('#selected_patient').show();
            
            // پاک کردن فرم‌ها
            clearNewPatientForm();
            $('#new_patient_form').hide();
            $('#patient_search').val('');
            $('#create_patient_btn').hide();
            
            showSuccessMessage('بیمار جدید با موفقیت ایجاد و انتخاب شد');
            
            // برای بیمار جدید نیازی به بارگیری طرح درمان قبلی نیست
        },
        error: function(xhr) {
            let errorMessage = 'خطا در ایجاد بیمار';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join('\n');
            }
            
            alert(errorMessage);
        },
        complete: function() {
            // برگرداندن حالت دکمه
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }
    });
}

function cancelNewPatient() {
    $('#new_patient_form').hide();
    $('#patient_search').val('');
    $('#create_patient_btn').hide();
    $('#patient_dropdown').hide();
    $('#patient_search').focus();
}

function cancelTreatmentSelection() {
    $('.tooth').removeClass('selected');
    selectedTooth = null;
    // Remove reference to old treatment selection form
}

function addTreatment() {
    // This function is no longer needed as we're using the dropdown approach
    // But keeping it for backward compatibility
    const treatmentType = $('#treatment_type').val();
    const treatmentText = $('#treatment_type option:selected').text();
    const cost = parseInt($('#treatment_cost').val()) || 0;
    const notes = $('#treatment_notes').val();
    
    if (!treatmentType) {
        alert('لطفا نوع درمان را انتخاب کنید');
        return;
    }
    
    // گرفتن اطلاعات دندان انتخاب شده
    const selectedToothElement = $('.tooth.selected');
    const displayNumber = selectedToothElement.data('display');
    const jaw = selectedToothElement.data('jaw');
    const side = selectedToothElement.data('side');
    
    const jawText = jaw === 'upper' ? 'بالا' : 'پایین';
    const sideText = side === 'right' ? 'چپ' : 'راست';
    const persianNumber = displayNumber.toString().replace(/\d/g, function(match) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return persianDigits[parseInt(match)];
    });
    const toothDescription = `دندان ${persianNumber} ${jawText} ${sideText}`;
    
    // Remove existing treatment for this tooth
    treatments = treatments.filter(t => t.tooth !== selectedTooth);
    
    // Add new treatment
    treatments.push({
        tooth: selectedTooth,
        toothDescription: toothDescription,
        treatmentType: treatmentType,
        treatmentText: treatmentText,
        cost: cost,
        notes: notes
    });
    
    // Mark tooth as having treatment
    $(`.tooth[data-tooth="${selectedTooth}"]`).addClass('has-treatment');
    
    updateTreatmentsList();
    cancelTreatmentSelection();
}

function removeTreatment(tooth) {
    treatments = treatments.filter(t => t.tooth !== tooth);
    $(`.tooth[data-tooth="${tooth}"]`).removeClass('has-treatment');
    updateTreatmentsList();
}

function editTreatment(tooth) {
    const treatment = treatments.find(t => t.tooth === tooth);
    if (!treatment) return;
    
    // پیدا کردن عنصر دندان
    const toothElement = $(`.tooth[data-tooth="${tooth}"]`);
    
    // انتخاب دندان برای ویرایش
    $('.tooth').removeClass('selected');
    toothElement.addClass('selected');
    selectedTooth = tooth;
    
    // پر کردن فرم با اطلاعات موجود
    toothElement.find('.treatment-select').val(treatment.treatmentType);
    
    // نمایش عنوان دندان
    const displayNumber = toothElement.data('display');
    const jaw = toothElement.data('jaw');
    const side = toothElement.data('side');
    
    const jawText = jaw === 'upper' ? 'بالا' : 'پایین';
    const sideText = side === 'right' ? 'چپ' : 'راست';
    const persianNumber = displayNumber.toString().replace(/\d/g, function(match) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return persianDigits[parseInt(match)];
    });
    const toothDescription = `دندان ${persianNumber} ${jawText} ${sideText}`;
    
    // حذف درمان قبلی از لیست (برای ویرایش)
    treatments = treatments.filter(t => t.tooth !== tooth);
    toothElement.removeClass('has-treatment');
    updateTreatmentsList();
}

function removeToothTreatments(tooth) {
    treatments = treatments.filter(t => t.tooth !== tooth);
    $(`.tooth[data-tooth="${tooth}"]`).removeClass('has-treatment has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11');
    updateTreatmentsList();
}

function editToothTreatments(tooth) {
    // Find the tooth element
    const toothElement = $(`.tooth[data-tooth="${tooth}"]`);
    
    // Select the tooth for editing
    $('.tooth').removeClass('selected');
    toothElement.addClass('selected');
    selectedTooth = tooth;
    
    // Show tooth description
    const toothData = treatments.find(t => t.tooth === tooth);
    if (toothData) {
        $('#selected_tooth_number').text(toothData.toothDescription);
    }
    
    // Remove all treatments for this tooth from the list (for re-editing)
    treatments = treatments.filter(t => t.tooth !== tooth);
    toothElement.removeClass('has-treatment');
    updateTreatmentsList();
}

function saveTreatmentPlan() {
    if (!selectedPatientId) {
        alert('لطفا بیمار را انتخاب کنید');
        return;
    }
    
    if (treatments.length === 0) {
        alert('لطفا حداقل یک درمان اضافه کنید');
        return;
    }
    
    const data = {
        patient_id: selectedPatientId,
        treatments: treatments,
        total_cost: totalCost,
        _token: '{{ csrf_token() }}'
    };
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> در حال ذخیره...';
    btn.disabled = true;
    
    $.ajax({
        url: '{{ route("treatments.store") }}',
        method: 'POST',
        data: data,
        success: function(response) {
            // نمایش پیام موفقیت آمیز سبز رنگ
            showSuccessMessage('طرح درمان با موفقیت ثبت شد');
            
            // بعد از 2 ثانیه ریدایرکت به لیست طرح‌های درمان
            setTimeout(function() {
                window.location.href = '{{ route("treatments.index") }}';
            }, 2000);
        },
        error: function(xhr) {
            alert('خطا در ثبت طرح درمان');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}

// تابع بازگشت آخرین درمان اضافه شده
function undoLastTreatment() {
    if (lastAddedTreatment) {
        // حذف آخرین درمان اضافه شده
        treatments = treatments.filter(t => !(t.tooth === lastAddedTreatment.tooth && t.treatmentType === lastAddedTreatment.treatmentType));
        
        // اگر درمان دیگری برای همین دندان وجود نداشت، کلاس‌ها را پاک کن
        const toothHasOtherTreatments = treatments.some(t => t.tooth === lastAddedTreatment.tooth);
        if (!toothHasOtherTreatments) {
            $(`.tooth[data-tooth="${lastAddedTreatment.tooth}"]`).removeClass('has-treatment has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11');
        }
        
        // به روزرسانی لیست درمان‌ها
        updateTreatmentsList();
        
        // پنهان کردن دکمه Undo
        $('#undo_treatment_btn').hide();
        lastAddedTreatment = null;
        
        // نمایش پیام موفقیت
        showSuccessMessage('آخرین درمان اضافه شده با موفقیت حذف شد');
    }
}

// تابع پاک کردن کل چارت
function clearDentalChart() {
    if (treatments.length === 0) {
        showInfoMessage('چارت درمان خالی است');
        return;
    }
    
    // تایید کاربر قبل از پاک کردن
    if (confirm('آیا از پاک کردن کل چارت درمان اطمینان دارید؟')) {
        // ذخیره تمام درمان‌ها برای قابلیت Undo
        const allTreatments = [...treatments];
        
        // پاک کردن تمام درمان‌ها
        treatments = [];
        totalCost = 0;
        
        // پاک کردن کلاس‌های تمام دندان‌ها
        $('.tooth').removeClass('has-treatment has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11');
        
        // به روزرسانی لیست درمان‌ها
        updateTreatmentsList();
        
        // پنهان کردن دکمه Undo
        $('#undo_treatment_btn').hide();
        lastAddedTreatment = null;
        
        // نمایش پیام موفقیت
        showSuccessMessage('کل چارت درمان با موفقیت پاک شد');
    }
}

// تابع ریست کردن چارت دندان
function resetDentalChart() {
    // پاک کردن تمام درمان‌های انتخاب شده از چارت
    $('.tooth').removeClass('has-treatment has-treatment-1 has-treatment-2 has-treatment-3 has-treatment-4 has-treatment-5 has-treatment-6 has-treatment-7 has-treatment-8 has-treatment-9 has-treatment-10 has-treatment-11 selected');
    
    // پنهان کردن دکمه‌های حذف
    $('.btn-remove-treatment').hide();
    
    // ریست کردن دستگاه‌های انتخاب درمان
    $('.treatment-select').val('');
}
</script>
@endpush