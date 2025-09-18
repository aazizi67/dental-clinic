@extends('layouts.app')

@section('title', 'گزارش ورود و خروج')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">گزارش ورود و خروج</h1>
        <p class="text-muted mb-0">مشاهده و فیلتر گزارش ورود و خروج پرسنل</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            بازگشت به ورود و خروج
        </a>
        <a href="{{ route('attendance.working-hours-report') }}" class="btn btn-outline-success ms-2">
            <i class="bi bi-currency-dollar me-1"></i>
            گزارش ساعت کاری
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.report') }}">
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

<!-- Entry/Exit Report -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">گزارش ورود و خروج</h5>
    </div>
    <div class="card-body">
        @if($records->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>نام پرسنل</th>
                            <th>سمت</th>
                            <th>ساعت ورود</th>
                            <th>ساعت خروج</th>
                            <th>ساعت کاری</th>
                            <th>روش ثبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                        <tr>
                            <td>{{ verta($record->date)->format('Y/m/d') }}</td>
                            <td>{{ $record->staff->getFullNameAttribute() }}</td>
                            <td>
                                @switch($record->staff->role)
                                    @case('doctor')
                                        <span class="badge bg-primary">دکتر</span>
                                        @break
                                    @case('secretary')
                                        <span class="badge bg-info">منشی</span>
                                        @break
                                    @case('assistant')
                                        <span class="badge bg-secondary">کمک‌کار</span>
                                        @break
                                    @case('nurse')
                                        <span class="badge bg-success">پرستار</span>
                                        @break
                                    @case('cleaner')
                                        <span class="badge bg-warning">نگهبان/تمیزکار</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">سایر</span>
                                @endswitch
                            </td>
                            <td>{{ $record->check_in_time ?? '-' }}</td>
                            <td>{{ $record->check_out_time ?? '-' }}</td>
                            <td>
                                @if($record->check_in_time && $record->check_out_time)
                                    {{ number_format($record->getWorkingHours(), 2) }} ساعت
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($record->check_in_time)
                                    <small class="d-block">ورود: {{ $record->check_in_method }}</small>
                                @endif
                                @if($record->check_out_time)
                                    <small class="d-block">خروج: {{ $record->check_out_method }}</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $records->appends(request()->query())->links() }}
            </div>
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