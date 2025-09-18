@extends('layouts.app')

@section('title', 'گزارش‌های لابراتوار')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">گزارش‌های لابراتوار</h1>
        <p class="text-muted mb-0">مشاهده و فیلتر گزارش‌های لابراتوار</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('laboratories.transactions') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            بازگشت به تراکنش‌ها
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('laboratories.reports') }}">
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
                    <label for="laboratory_id" class="form-label">لابراتوار</label>
                    <select class="form-select" id="laboratory_id" name="laboratory_id">
                        <option value="">همه لابراتوارها</option>
                        @foreach($laboratories as $laboratory)
                            <option value="{{ $laboratory->id }}" {{ request('laboratory_id') == $laboratory->id ? 'selected' : '' }}>
                                {{ $laboratory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">دسته‌بندی</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">همه دسته‌بندی‌ها</option>
                        @foreach($categories as $key => $value)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>
                        فیلتر
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reports -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">گزارش تراکنش‌ها</h5>
    </div>
    <div class="card-body">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>ساعت</th>
                            <th>لابراتوار</th>
                            <th>بیمار</th>
                            <th>دندانپزشک</th>
                            <th>نوع</th>
                            <th>دسته‌بندی</th>
                            <th>قیمت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ verta($transaction->date)->format('Y/m/d') }}</td>
                            <td>{{ $transaction->time }}</td>
                            <td>{{ $transaction->laboratory->name }}</td>
                            <td>{{ $transaction->patient->full_name }}</td>
                            <td>{{ $transaction->doctor->name ?? '-' }}</td>
                            <td>
                                @if($transaction->type == 'entry')
                                    <span class="badge bg-warning">خروجی (ارسال به لابراتوار)</span>
                                @else
                                    <span class="badge bg-success">ورودی (دریافت از لابراتوار)</span>
                                @endif
                            </td>
                            <td>
                                @switch($transaction->category)
                                    @case('post')
                                        پست
                                        @break
                                    @case('crown')
                                        روکش
                                        @break
                                    @case('laminat')
                                        لمینت
                                        @break
                                    @case('implant_crown')
                                        روکش ایمپلنت
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $transaction->price ? number_format($transaction->price, 0) . ' تومان' : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $transactions->appends(request()->query())->links() }}
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