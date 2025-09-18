@extends('layouts.app')

@section('title', 'مدیریت نوبت‌ها')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">مدیریت نوبت‌ها</h1>
        <p class="text-muted mb-0">مشاهده و مدیریت تمام نوبت‌های مطب</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('appointments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            نوبت جدید
        </a>
    </div>
</div>

<!-- فیلترها -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('appointments.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <label for="date_fa" class="form-label">تاریخ</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="date_fa" placeholder="مثلاً ۱۴۰۳/۰۹/۲۲" autocomplete="off" readonly>
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    </div>
                    <input type="hidden" id="date" name="date" value="{{ request('date') }}">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">وضعیت</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>زمان‌بندی شده</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search me-1"></i>
                        جستجو
                    </button>
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>
                        پاک کردن
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- لیست نوبت‌ها -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">لیست نوبت‌ها</h5>
    </div>
    <div class="card-body p-0">
        @if($appointments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>بیمار</th>
                            <th>تاریخ</th>
                            <th>زمان</th>
                            <th>مدت</th>
                            <th>نوع</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $appointment->patient->full_name ?? 'نامشخص' }}</div>
                                        <small class="text-muted">{{ $appointment->patient->phone ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $appointment->appointment_date ? \App\Helpers\PersianDateHelper::toPersian($appointment->appointment_date) : '-' }}</td>
                            <td>{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') : '-' }}</td>
                            <td>{{ $appointment->duration ?? '-' }} دقیقه</td>
                            <td>
                                @switch($appointment->type)
                                    @case('consultation')
                                        <span class="badge bg-info">مشاوره</span>
                                        @break
                                    @case('treatment')
                                        <span class="badge bg-primary">درمان</span>
                                        @break
                                    @case('follow_up')
                                        <span class="badge bg-warning">پیگیری</span>
                                        @break
                                    @case('emergency')
                                        <span class="badge bg-danger">اورژانس</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">نامشخص</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($appointment->status)
                                    @case('scheduled')
                                        <span class="badge bg-primary">زمان‌بندی شده</span>
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
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-info" title="مشاهده">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-warning" title="ویرایش">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer">
                {{ $appointments->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">هیچ نوبتی یافت نشد!</h5>
                <p class="text-muted">می‌توانید نوبت جدید ایجاد کنید</p>
                <a href="{{ route('appointments.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-1"></i>
                    نوبت جدید
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // تنظیم دیت پیکر شمسی برای فیلتر تاریخ
    var tries = 0;
    var iv = setInterval(function(){
        tries++;
        if (window.jQuery && jQuery.fn && typeof jQuery.fn.persianDatepicker === 'function') {
            clearInterval(iv);
            var $filterDate = jQuery('#date_fa');
            $filterDate.attr('readonly', true).persianDatepicker({
                format: 'YYYY/MM/DD',
                altField: '#date',
                altFormat: 'YYYY-MM-DD',
                initialValue: false,
                initialValueType: 'persian',
                autoClose: true,
                calendar: { persian: { locale: 'fa' } },
                toolbox: { todayButton: { enabled: true } }
            });
            var filterPicker = $filterDate.data('datepicker');
            var $filterIcon = $filterDate.closest('.input-group').find('.input-group-text');
            $filterDate.on('click focus', function(){ if (filterPicker) filterPicker.show(); });
            $filterIcon.on('click', function(){ if (filterPicker) filterPicker.show(); });
            
            // اگر مقدار قبلی داریم، نمایشش بده
            const currentDate = $('#date').val();
            if (currentDate && filterPicker) {
                filterPicker.setDate(new Date(currentDate));
            }
        }
        if (tries > 50) clearInterval(iv);
    }, 100);
});
</script>