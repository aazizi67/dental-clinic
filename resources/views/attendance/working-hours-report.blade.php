@extends('layouts.app')

@section('title', 'گزارش ساعت کاری و حقوق')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">گزارش ساعت کاری و حقوق</h1>
        <p class="text-muted mb-0">گزارش تفصیلی ساعت کاری و محاسبه حقوق پرسنل</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            بازگشت به ورود و خروج
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.working-hours-report') }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">تاریخ شروع</label>
                    <input type="text" class="form-control" id="start_date" name="start_date" value="{{ request('start_date', verta($startDate)->format('Y/m/d')) }}" placeholder="مثلاً ۱۴۰۳/۰۹/۲۲" autocomplete="off" readonly>
                    <input type="hidden" id="start_date_gregorian" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">تاریخ پایان</label>
                    <input type="text" class="form-control" id="end_date" name="end_date" value="{{ request('end_date', verta($endDate)->format('Y/m/d')) }}" placeholder="مثلاً ۱۴۰۳/۰۹/۲۲" autocomplete="off" readonly>
                    <input type="hidden" id="end_date_gregorian" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label for="staff_id" class="form-label">پرسنل</label>
                    <select class="form-select" id="staff_id" name="staff_id">
                        <option value="">همه پرسنل</option>
                        @foreach($staff as $person)
                            <option value="{{ $person->id }}" {{ request('staff_id') == $person->id ? 'selected' : '' }}>
                                {{ $person->getFullNameAttribute() }}
                                (@switch($person->role)
                                    @case('doctor')
                                        دکتر
                                        @break
                                    @case('secretary')
                                        منشی
                                        @break
                                    @case('assistant')
                                        کمک‌کار
                                        @break
                                    @case('nurse')
                                        پرستار
                                        @break
                                    @case('cleaner')
                                        نگهبان/تمیزکار
                                        @break
                                    @default
                                        سایر
                                @endswitch)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>
                        فیلتر
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-6 border-end">
                <h5>مجموع ساعات کاری</h5>
                <h2 class="text-primary">{{ number_format($totalHours, 2) }} ساعت</h2>
            </div>
            <div class="col-md-6">
                <h5>مجموع حقوق پرداختنی</h5>
                <h2 class="text-success">{{ number_format($totalSalary, 0) }} تومان</h2>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Report -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">گزارش تفصیلی ساعت کاری</h5>
    </div>
    <div class="card-body">
        @if(count($staffHours) > 0)
            @foreach($staffHours as $staffData)
            <div class="mb-4 pb-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">{{ $staffData['staff']->getFullNameAttribute() }}</h5>
                        <p class="text-muted mb-0">
                            @switch($staffData['staff']->role)
                                @case('doctor')
                                    دکتر
                                    @break
                                @case('secretary')
                                    منشی
                                    @break
                                @case('assistant')
                                    کمک‌کار
                                    @break
                                @case('nurse')
                                    پرستار
                                    @break
                                @case('cleaner')
                                    نگهبان/تمیزکار
                                    @break
                                @default
                                    سایر
                            @endswitch
                            | نرخ ساعتی: {{ number_format($staffData['staff']->hourly_rate, 0) }} تومان
                        </p>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-1">مجموع: {{ number_format($staffData['total_hours'], 2) }} ساعت</h6>
                        <h6 class="text-success mb-0">حقوق: {{ number_format($staffData['total_salary'], 0) }} تومان</h6>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>ساعت ورود</th>
                                <th>ساعت خروج</th>
                                <th>ساعت کاری</th>
                                <th>حقوق روزانه</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffData['records'] as $record)
                            <tr>
                                <td>{{ verta($record->date)->format('Y/m/d') }}</td>
                                <td>{{ $record->check_in_time }}</td>
                                <td>{{ $record->check_out_time }}</td>
                                <td>{{ number_format($record->getWorkingHours(), 2) }} ساعت</td>
                                <td>{{ number_format($record->getDailySalary(), 0) }} تومان</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="3">جمع کل برای {{ $staffData['staff']->getFullNameAttribute() }}:</th>
                                <th>{{ number_format($staffData['total_hours'], 2) }} ساعت</th>
                                <th>{{ number_format($staffData['total_salary'], 0) }} تومان</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">هیچ رکوردی یافت نشد!</h5>
                <p class="text-muted">برای مشاهده گزارش، فیلترهای خود را تغییر دهید</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    $('#start_date').MdPersianDateTimePicker({
        targetTextSelector: '#start_date',
        targetDateSelector: '#start_date_gregorian',
        dateFormat: 'yyyy/MM/dd',
        isGregorian: false,
        modalMode: false,
        englishNumber: false,
        enableTimePicker: false,
        groupId: 'date-range'
    });
    
    $('#end_date').MdPersianDateTimePicker({
        targetTextSelector: '#end_date',
        targetDateSelector: '#end_date_gregorian',
        dateFormat: 'yyyy/MM/dd',
        isGregorian: false,
        modalMode: false,
        englishNumber: false,
        enableTimePicker: false,
        groupId: 'date-range'
    });
});
</script>
@endpush