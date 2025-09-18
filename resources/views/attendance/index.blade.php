@extends('layouts.app')

@section('title', 'ورود و خروج پرسنل')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">ورود و خروج پرسنل</h1>
        <p class="text-muted mb-0">ثبت ورود و خروج پرسنل کلینیک</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('attendance.report') }}" class="btn btn-outline-primary">
            <i class="bi bi-file-text me-1"></i>
            گزارش ورود و خروج
        </a>
        <a href="{{ route('attendance.working-hours-report') }}" class="btn btn-outline-success ms-2">
            <i class="bi bi-currency-dollar me-1"></i>
            گزارش ساعت کاری
        </a>
    </div>
</div>

<!-- Current Staff Entry/Exit Card -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">ثبت ورود/خروج شما</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="mb-0">{{ auth()->user()->name ?? 'کاربر' }}</h6>
                <small class="text-muted">امروز: {{ verta()->format('l, d F Y') }}</small>
            </div>
            <div class="col-md-6 text-md-end">
                @php
                    // Find the staff record for the current user if exists
                    $currentStaff = \App\Models\Staff::where('phone', auth()->user()->phone ?? '')->first();
                    $staffAttendance = null;
                    if($currentStaff) {
                        $staffAttendance = $attendanceRecords->get($currentStaff->id);
                    }
                @endphp
                
                @if($currentStaff && $staffAttendance && $staffAttendance->isCheckedOut())
                    <span class="badge bg-success">خروج ثبت شده</span>
                    <div class="mt-2">
                        <small class="text-muted">ورود: {{ $staffAttendance->check_in_time }}</small><br>
                        <small class="text-muted">خروج: {{ $staffAttendance->check_out_time }}</small>
                    </div>
                @elseif($currentStaff && $staffAttendance && $staffAttendance->isCheckedIn())
                    <span class="badge bg-warning">فقط ورود ثبت شده</span>
                    <div class="mt-2">
                        <small class="text-muted">ورود: {{ $staffAttendance->check_in_time }}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger mt-2" id="checkoutBtn" data-staff-id="{{ $currentStaff->id }}">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        ثبت خروج
                    </button>
                @elseif($currentStaff)
                    <button class="btn btn-sm btn-outline-success" id="checkinBtn" data-staff-id="{{ $currentStaff->id }}">
                        <i class="bi bi-box-arrow-in-left me-1"></i>
                        ثبت ورود
                    </button>
                @else
                    <span class="badge bg-secondary">پرسنل ثبت نشده</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- All Staff Entry/Exit -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">وضعیت ورود و خروج پرسنل</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>نام پرسنل</th>
                        <th>سمت</th>
                        <th>وضعیت امروز</th>
                        <th>ساعت ورود</th>
                        <th>ساعت خروج</th>
                        <th>ساعت کاری</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $person)
                    @php
                        $record = $attendanceRecords->get($person->id);
                    @endphp
                    <tr>
                        <td>{{ $person->getFullNameAttribute() }}</td>
                        <td>
                            @switch($person->role)
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
                        <td>
                            @if($record)
                                @if($record->isCheckedOut())
                                    <span class="badge bg-success">خروج ثبت شده</span>
                                @elseif($record->isCheckedIn())
                                    <span class="badge bg-warning">فقط ورود</span>
                                @endif
                            @else
                                <span class="badge bg-danger">ثبت نشده</span>
                            @endif
                        </td>
                        <td>{{ $record ? $record->check_in_time : '-' }}</td>
                        <td>{{ $record ? $record->check_out_time : '-' }}</td>
                        <td>
                            @if($record && $record->isCheckedOut())
                                {{ number_format($record->getWorkingHours(), 2) }} ساعت
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check-in button
    const checkinBtn = document.getElementById('checkinBtn');
    if (checkinBtn) {
        checkinBtn.addEventListener('click', function() {
            const staffId = this.getAttribute('data-staff-id');
            fetch('{{ route("attendance.check-in") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    staff_id: staffId,
                    method: 'manual'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('خطا در ثبت ورود', 'error');
            });
        });
    }
    
    // Check-out button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            const staffId = this.getAttribute('data-staff-id');
            fetch('{{ route("attendance.check-out") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    staff_id: staffId,
                    method: 'manual'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('خطا در ثبت خروج', 'error');
            });
        });
    }
    
    // Show alert function
    function showAlert(message, type) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.left = '50%';
        alertDiv.style.transform = 'translateX(-50%)';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Add to body
        document.body.appendChild(alertDiv);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>
@endpush