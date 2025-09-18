@extends('layouts.app')

@section('title', 'جزئیات پرسنل')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">جزئیات پرسنل</h1>
        <p class="text-muted mb-0">اطلاعات کامل پرسنل و سوابق حضور و غیاب</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('staff.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            بازگشت به لیست پرسنل
        </a>
        <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-outline-warning ms-2">
            <i class="bi bi-pencil me-1"></i>
            ویرایش
        </a>
    </div>
</div>

<!-- Staff Information Card -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">اطلاعات پرسنل</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th>نام و نام خانوادگی:</th>
                        <td>{{ $staff->getFullNameAttribute() }}</td>
                    </tr>
                    <tr>
                        <th>کد ملی:</th>
                        <td>{{ $staff->national_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>تلفن:</th>
                        <td>{{ $staff->phone }}</td>
                    </tr>
                    <tr>
                        <th>سمت:</th>
                        <td>
                            @switch($staff->role)
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
                    </tr>
                    <tr>
                        <th>آدرس:</th>
                        <td>{{ $staff->address ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th>تاریخ استخدام:</th>
                        <td>{{ $staff->hire_date ? verta($staff->hire_date)->format('Y/m/d') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>تاریخ تولد:</th>
                        <td>{{ $staff->birth_date ? verta($staff->birth_date)->format('Y/m/d') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>نرخ ساعتی:</th>
                        <td>{{ number_format($staff->hourly_rate, 0) }} تومان</td>
                    </tr>
                    <tr>
                        <th>وضعیت:</th>
                        <td>
                            @if($staff->is_active)
                                <span class="badge bg-success">فعال</span>
                            @else
                                <span class="badge bg-danger">غیرفعال</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>تماس اضطراری:</th>
                        <td>
                            {{ $staff->emergency_contact_name ?? '-' }}<br>
                            {{ $staff->emergency_contact_phone ?? '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Records Card -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">سوابق ورود و خروج</h5>
    </div>
    <div class="card-body">
        @if($attendanceRecords->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>ساعت ورود</th>
                            <th>ساعت خروج</th>
                            <th>ساعت کاری</th>
                            <th>حقوق روزانه</th>
                            <th>روش ثبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendanceRecords as $record)
                        <tr>
                            <td>{{ verta($record->date)->format('Y/m/d') }}</td>
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
                                @if($record->check_in_time && $record->check_out_time)
                                    {{ number_format($record->getDailySalary(), 0) }} تومان
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
                {{ $attendanceRecords->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">هیچ رکوردی یافت نشد!</h5>
                <p class="text-muted">این پرسنل هنوز ورود و خروجی ندارد</p>
            </div>
        @endif
    </div>
</div>
@endsection