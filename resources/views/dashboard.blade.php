@extends('layouts.app')

@section('title', 'داشبورد')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">داشبورد</h1>
        <p class="text-muted mb-0">نمای کلی از فعالیت‌های مطب</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-1"></i>
                گزارش کامل
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i>
                پرینت
            </button>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="refreshDashboard()">
            <i class="bi bi-arrow-clockwise me-1"></i>
            به‌روزرسانی
        </button>
    </div>
</div>

<!-- میانبرهای سریع -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h5 class="mb-3">
            <i class="bi bi-lightning-charge text-warning me-2"></i>
            میانبرهای سریع
        </h5>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ route('patients.create') }}" class="card quick-action-card text-decoration-none h-100">
            <div class="card-body text-center">
                <div class="quick-action-icon mb-3">
                    <i class="bi bi-person-plus" style="font-size: 2.5rem; color: #10B981;"></i>
                </div>
                <h5 class="text-dark mb-2">بیمار جدید</h5>
                <p class="text-muted small mb-0">ثبت بیمار جدید در سیستم</p>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ route('treatments.create') }}" class="card quick-action-card text-decoration-none h-100">
            <div class="card-body text-center">
                <div class="quick-action-icon mb-3">
                    <i class="bi bi-clipboard2-pulse" style="font-size: 2.5rem; color: #3B82F6;"></i>
                </div>
                <h5 class="text-dark mb-2">معاینه جدید</h5>
                <p class="text-muted small mb-0">ثبت معاینه و طرح درمان</p>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ route('appointments.create') }}" class="card quick-action-card text-decoration-none h-100">
            <div class="card-body text-center">
                <div class="quick-action-icon mb-3">
                    <i class="bi bi-calendar-plus" style="font-size: 2.5rem; color: #F59E0B;"></i>
                </div>
                <h5 class="text-dark mb-2">نوبت جدید</h5>
                <p class="text-muted small mb-0">رزرو نوبت برای بیمار</p>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ route('accounting.transactions.create') }}" class="card quick-action-card text-decoration-none h-100">
            <div class="card-body text-center">
                <div class="quick-action-icon mb-3">
                    <i class="bi bi-cash-coin" style="font-size: 2.5rem; color: #8B5CF6;"></i>
                </div>
                <h5 class="text-dark mb-2">تراکنش جدید</h5>
                <p class="text-muted small mb-0">ثبت درآمد یا هزینه</p>
            </div>
        </a>
    </div>
</div>

<!-- برنامه امروز و نوبت‌های آینده -->
<div class="row">
    <!-- نوبت‌های امروز -->
    <div class="col-lg-12 mb-4">
        <div class="card border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check text-success me-2"></i>
                    برنامه امروز
                    <span class="badge bg-success ms-2">{{ $todayAppointments->count() }} نوبت</span>
                </h5>
                <div class="d-flex align-items-center">
                    <small class="text-muted me-3">
                        {{ now()->locale('fa')->translatedFormat('ل، d F Y') }}
                    </small>
                    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        نوبت جدید
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($todayAppointments->count() > 0)
                    <div class="timeline-container">
                        @foreach($todayAppointments as $appointment)
                        <div class="timeline-item {{ $appointment->isPast() ? 'completed' : ($appointment->isToday() ? 'active' : 'upcoming') }}">
                            <div class="timeline-time">
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('patients.show', $appointment->patient) }}" class="text-decoration-none">
                                                {{ $appointment->patient->full_name ?? 'نامشخص' }}
                                            </a>
                                        </h6>
                                        <p class="text-muted mb-2 small">
                                            <i class="bi bi-telephone me-1"></i>
                                            {{ $appointment->patient->phone ?? '-' }}
                                        </p>
                                        @if($appointment->chief_complaint)
                                            <p class="mb-1 small text-info">
                                                <i class="bi bi-chat-left-text me-1"></i>
                                                {{ $appointment->chief_complaint }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @if($appointment->status == 'scheduled')
                                            <span class="badge bg-primary">زمان‌بندی شده</span>
                                        @elseif($appointment->status == 'completed')
                                            <span class="badge bg-success">تکمیل شده</span>
                                        @else
                                            <span class="badge bg-danger">لغو شده</span>
                                        @endif
                                        <div class="mt-2">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-success btn-sm" title="تکمیل">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button class="btn btn-outline-info btn-sm" title="ویرایش">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">امروز نوبتی ندارید!</h5>
                        <p class="text-muted">می‌توانید از این زمان برای کارهای اداری استفاده کنید</p>
                        <a href="{{ route('appointments.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle me-1"></i>
                            نوبت جدید
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- بخش نوبت‌های آینده -->
<div class="row mb-4">
    <!-- نوبت‌های آینده -->
    <div class="col-lg-12 mb-4">
        <div class="card border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar2-week text-warning me-2"></i>
                    نوبت‌های آینده (3 روز)
                </h5>
            </div>
            <div class="card-body p-0">
                @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
                    <div class="upcoming-appointments">
                        @foreach($upcomingAppointments->groupBy('appointment_date') as $date => $appointments)
                        <div class="day-group">
                            <div class="day-header px-3 py-2">
                                <h6 class="mb-0 text-primary">
                                    {{ \App\Helpers\PersianDateHelper::toPersian($date) }}
                                    <span class="badge bg-primary ms-2">{{ $appointments->count() }} نوبت</span>
                                </h6>
                            </div>
                            <div class="appointments-list px-3">
                                @foreach($appointments as $appointment)
                                <div class="appointment-item py-2 border-bottom">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="time-badge badge bg-light text-dark">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                            </div>
                                            <div class="ms-3">
                                                <span class="fw-medium">{{ $appointment->patient->full_name }}</span>
                                                @if($appointment->chief_complaint)
                                                    <br><small class="text-muted">{{ Str::limit($appointment->chief_complaint, 30) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary" title="مشاهده">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar2-week text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">نوبتی برای 3 روز آینده ندارید!</h5>
                        <p class="text-muted">می‌توانید برای بیماران نوبت جدید تعیین کنید</p>
                        <a href="{{ route('appointments.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle me-1"></i>
                            نوبت جدید
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
    // به‌روزرسانی داشبورد
    function refreshDashboard() {
        const refreshBtn = $('button:contains("به‌روزرسانی")');
        const originalText = refreshBtn.html();
        
        refreshBtn.html('<i class="bi bi-arrow-clockwise me-1 spin"></i> در حال به‌روزرسانی...');
        refreshBtn.prop('disabled', true);
        
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
    
    // انیمیشن میانبرهای سریع هنگام لود
    $(document).ready(function() {
        $('.quick-action-card').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(30px)'
            }).delay(index * 100).animate({
                'opacity': 1
            }, {
                duration: 600,
                step: function(now) {
                    $(this).css('transform', 'translateY(' + (30 - (30 * now)) + 'px)');
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
/* انیمیشن چرخش */
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* استایل میانبرهای سریع */
.quick-action-card {
    background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);
    transition: all 0.3s ease;
    border-radius: 16px;
    border: 2px solid transparent;
    overflow: hidden;
    position: relative;
}

.quick-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    border-color: rgba(59, 130, 246, 0.2);
}

.quick-action-card:hover .quick-action-icon i {
    transform: scale(1.1);
}

.quick-action-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: rgba(59, 130, 246, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    transition: all 0.3s ease;
}

.quick-action-icon i {
    transition: all 0.3s ease;
}

.quick-action-card h5 {
    font-weight: 600;
    color: #1F2937;
}

.quick-action-card:hover h5 {
    color: #3B82F6;
}

/* استایل تایم‌لاین */
.timeline-container {
    padding: 1rem;
}

.timeline-item {
    display: flex;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 70px;
    top: 0;
    bottom: -1.5rem;
    width: 2px;
    background: #E2E8F0;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-time {
    width: 60px;
    flex-shrink: 0;
    font-weight: 600;
    color: var(--primary-color);
    font-size: 0.9rem;
    text-align: center;
    padding: 0.5rem;
    background: var(--primary-light);
    border-radius: 8px;
    margin-left: 1rem;
    position: relative;
    z-index: 1;
}

.timeline-content {
    flex: 1;
    background: white;
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #E2E8F0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.timeline-item.completed .timeline-time {
    background: #D1FAE5;
    color: #065F46;
}

.timeline-item.completed .timeline-content {
    border-color: #10B981;
    background: #F0FDF4;
}

.timeline-item.active .timeline-time {
    background: var(--primary-color);
    color: white;
    animation: pulse 2s infinite;
}

/* استایل نوبت‌های آینده */
.day-group {
    border-bottom: 1px solid #E2E8F0;
}

.day-group:last-child {
    border-bottom: none;
}

.day-header {
    background: linear-gradient(135deg, #F8FAFC, #F1F5F9);
    border-bottom: 1px solid #E2E8F0;
}

.appointment-item {
    transition: all 0.2s ease;
    font-size: 1rem;
}

.appointment-item .fw-medium {
    font-size: 1.1rem;
    font-weight: 600;
}

.appointment-item .time-badge {
    font-size: 0.95rem;
    padding: 0.5rem 0.75rem;
}

.appointment-item:hover {
    background: rgba(59, 130, 246, 0.02);
    transform: translateX(5px);
}

.appointment-item:last-child {
    border-bottom: none !important;
}

/* بهبود responsive */
@media (max-width: 768px) {
    .timeline-item {
        flex-direction: column;
    }
    
    .timeline-time {
        width: auto;
        text-align: center;
        margin-bottom: 0.5rem;
    }
    
    .timeline-item::before {
        display: none;
    }
    
    .quick-action-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush