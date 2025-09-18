@php
function getToothDescription($toothNumber) {
    $quadrant = floor($toothNumber / 10);
    $position = $toothNumber % 10;
    
    switch($quadrant) {
        case 1: return $position . ' بالا سمت چپ';
        case 2: return $position . ' بالا سمت راست';
        case 3: return $position . ' پایین سمت راست';
        case 4: return $position . ' پایین سمت چپ';
        default: return $toothNumber;
    }
}
@endphp

@extends('layouts.app')

@section('title', 'مشاهده معاینه')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">مشاهده معاینه</h1>
        <p class="text-muted mb-0">{{ $treatment->title }}</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('treatments.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-right me-1"></i>
            بازگشت
        </a>
        @if($treatment->status !== 'completed')
        <a href="{{ route('treatments.edit', $treatment) }}" class="btn btn-warning me-2">
            <i class="bi bi-pencil me-1"></i>
            ویرایش
        </a>
        @endif
        <div class="btn-group">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots me-1"></i>
                عملیات
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="printTreatment()">
                    <i class="bi bi-printer me-2"></i>چاپ
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">
                    <i class="bi bi-trash me-2"></i>حذف
                </a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <!-- اطلاعات بیمار -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">اطلاعات بیمار</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-lg me-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            {{ substr($treatment->patient->first_name, 0, 1) }}
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $treatment->patient->full_name }}</h6>
                        <small class="text-muted">{{ $treatment->patient->phone }}</small>
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <small class="text-muted d-block">سن</small>
                            <strong>{{ $treatment->patient->age ?? '-' }} سال</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <small class="text-muted d-block">جنسیت</small>
                            <strong>
                                @if($treatment->patient->gender == 'male')
                                    آقا
                                @elseif($treatment->patient->gender == 'female')
                                    خانم
                                @else
                                    -
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- اطلاعات طرح درمان -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">اطلاعات طرح درمان</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">دکتر معالج</label>
                    <div class="fw-medium">{{ $treatment->doctor->name }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">تاریخ ایجاد</label>
                    <div class="fw-medium">{{ \Carbon\Carbon::parse($treatment->created_at)->format('Y/m/d H:i') }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">وضعیت</label>
                    <div>
                        @switch($treatment->status)
                            @case('draft')
                                <span class="badge bg-secondary">پیش‌نویس</span>
                                @break
                            @case('active')
                                <span class="badge bg-primary">فعال</span>
                                @break
                            @case('completed')
                                <span class="badge bg-success">تکمیل شده</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-danger">لغو شده</span>
                                @break
                            @default
                                <span class="badge bg-secondary">نامشخص</span>
                        @endswitch
                    </div>
                </div>

                <hr>
                
                <div class="mb-3">
                    <label class="form-label text-muted">هزینه کل</label>
                    <div class="fw-bold text-primary fs-5">{{ number_format($treatment->total_cost) }} ریال</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">پرداخت شده</label>
                    <div class="fw-medium text-success">{{ number_format($treatment->paid_amount) }} ریال</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">مانده</label>
                    <div class="fw-medium text-warning">{{ number_format($treatment->remaining_amount) }} ریال</div>
                </div>
                
                @if($treatment->remaining_amount > 0)
                <button class="btn btn-success w-100" onclick="showPaymentModal()">
                    <i class="bi bi-credit-card me-1"></i>
                    ثبت پرداخت
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- چارت دندانی و درمان‌ها -->
    <div class="col-lg-8">
        <!-- چارت دندانی -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">چارت دندانی</h5>
            </div>
            <div class="card-body">
                <div class="dental-chart">
                    <!-- فک بالا -->
                    <div class="jaw-section upper-jaw mb-4">
                        <h6 class="text-center mb-3">فک بالا</h6>
                        <div class="teeth-row">
                            <div class="side-label right-label">چپ</div>
                            <!-- دندان‌های راست -->
                            <div class="teeth-side right-side">
                                @for($i = 8; $i >= 1; $i--)
                                    @php $actualTooth = 10 + $i; @endphp
                                    <div class="tooth {{ $treatment->treatmentItems->where('tooth_number', $actualTooth)->count() > 0 ? 'has-treatment' : '' }}" 
                                         data-tooth="{{ $actualTooth }}" data-jaw="upper" data-side="right" data-display="{{ $i }}">
                                        <div class="tooth-number">{{ $i }}</div>
                                        <div class="tooth-body"></div>
                                        @if($treatment->treatmentItems->where('tooth_number', $actualTooth)->count() > 0)
                                            <div class="treatment-indicator"></div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                            <!-- دندان‌های چپ -->
                            <div class="teeth-side left-side">
                                @for($i = 1; $i <= 8; $i++)
                                    @php $actualTooth = 20 + $i; @endphp
                                    <div class="tooth {{ $treatment->treatmentItems->where('tooth_number', $actualTooth)->count() > 0 ? 'has-treatment' : '' }}" 
                                         data-tooth="{{ $actualTooth }}" data-jaw="upper" data-side="left" data-display="{{ $i }}">
                                        <div class="tooth-number">{{ $i }}</div>
                                        <div class="tooth-body"></div>
                                        @if($treatment->treatmentItems->where('tooth_number', $actualTooth)->count() > 0)
                                            <div class="treatment-indicator"></div>
                                        @endif
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
                            <!-- دندان‌های راست -->
                            <div class="teeth-side right-side">
                                @for($i = 8; $i >= 1; $i--)
                                    @php $actualTooth = 40 + $i; @endphp
                                    <div class="tooth {{ $treatment->treatmentItems->where('tooth_number', $actualTooth)->count() > 0 ? 'has-treatment' : '' }}" 
                                         data-tooth="{{ $actualTooth }}" data-jaw="lower" data-side="right" data-display="{{ $i }}">
                                        <div class="tooth-number">{{ $i }}</div>
                                        <div class="tooth-body"></div>
                                        @if($treatment->treatmentItems->where('tooth_number', $actualTooth)->count() > 0)
                                            <div class="treatment-indicator"></div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                            <!-- دندان‌های چپ -->
                            <div class="teeth-side left-side">
                                @for($i = 1; $i <= 8; $i++)
                                    @php $actualTooth = 30 + $i; @endphp
                                    <div class="tooth {{ $treatment->treatmentItems->where('tooth_number', $actualTooth)->count() > 0 ? 'has-treatment' : '' }}" 
                                         data-tooth="{{ $actualTooth }}" data-jaw="lower" data-side="left" data-display="{{ $i }}">
                                        <div class="tooth-number">{{ $i }}</div>
                                        <div class="tooth-body"></div>
                                        @if($treatment->treatmentItems->where('tooth_number', $actualTooth)->count() > 0)
                                            <div class="treatment-indicator"></div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                            <div class="side-label left-label">راست</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول درمان‌ها -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">لیست درمان‌ها</h5>
            </div>
            <div class="card-body">
                @if($treatment->treatmentItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>شماره دندان</th>
                                    <th>نوع درمان</th>
                                    <th>هزینه</th>
                                    <th>وضعیت</th>
                                    <th>توضیحات</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($treatment->treatmentItems as $item)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ getToothDescription($item->tooth_number) }}</span>
                                    </td>
                                    <td>{{ $item->treatment_type }}</td>
                                    <td>{{ number_format($item->cost) }} ریال</td>
                                    <td>
                                        @switch($item->status)
                                            @case('pending')
                                                <span class="badge bg-warning">در انتظار</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-info">در حال انجام</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">تکمیل شده</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">لغو شده</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">نامشخص</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td>
                                        @if($item->status !== 'completed')
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-success" title="تکمیل" 
                                                    onclick="updateTreatmentStatus({{ $item->id }}, 'completed')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="در حال انجام" 
                                                    onclick="updateTreatmentStatus({{ $item->id }}, 'in_progress')">
                                                <i class="bi bi-hourglass-split"></i>
                                            </button>
                                        </div>
                                        @else
                                        <span class="text-success">
                                            <i class="bi bi-check-circle"></i>
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">مجموع</th>
                                    <th>{{ number_format($treatment->total_cost) }} ریال</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-clipboard2-x text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">هیچ درمانی ثبت نشده است</h6>
                    </div>
                @endif
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
}

.tooth {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 5px;
    border-radius: 8px;
    position: relative;
}

.tooth.has-treatment {
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
}

.tooth.has-treatment .tooth-body {
    background: #fff;
    border-color: #fff;
}

.treatment-indicator {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: #dc3545;
    border-radius: 50%;
    border: 2px solid white;
}

.avatar-lg {
    width: 50px;
    height: 50px;
}
</style>
@endpush

@push('scripts')
<script>
function updateTreatmentStatus(itemId, status) {
    if (confirm('آیا از تغییر وضعیت این درمان اطمینان دارید؟')) {
        $.ajax({
            url: `/treatment-items/${itemId}/status`,
            method: 'PUT',
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('خطا در به‌روزرسانی وضعیت');
            }
        });
    }
}

function confirmDelete() {
    if (confirm('آیا از حذف این طرح درمان اطمینان دارید؟ این عمل قابل برگشت نیست.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("treatments.destroy", $treatment) }}';
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function printTreatment() {
    window.print();
}

function showPaymentModal() {
    // اینجا می‌توانید مودال پرداخت را نمایش دهید
    alert('قابلیت ثبت پرداخت به زودی اضافه خواهد شد');
}
</script>
@endpush