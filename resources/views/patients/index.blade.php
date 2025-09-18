@extends('layouts.app')

@section('title', 'لیست بیماران')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">بیماران</h1>
        <p class="text-muted mb-0">مدیریت و پیگیری اطلاعات بیماران</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-1"></i>
                خروجی Excel
            </button>
        </div>
        <a href="{{ route('patients.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            بیمار جدید
        </a>
    </div>
</div>

<!-- آمار سریع -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card border-0">
            <div class="card-body text-center">
                <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                <h3 class="mt-2 mb-1">{{ number_format($patients->total()) }}</h3>
                <p class="text-muted mb-0">کل بیماران</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card border-0">
            <div class="card-body text-center">
                <i class="bi bi-person-plus text-success" style="font-size: 2rem;"></i>
                <h3 class="mt-2 mb-1">{{ App\Models\Patient::whereDate('created_at', today())->count() }}</h3>
                <p class="text-muted mb-0">بیماران جدید امروز</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card border-0">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check text-warning" style="font-size: 2rem;"></i>
                <h3 class="mt-2 mb-1">{{ App\Models\Appointment::whereDate('appointment_date', today())->count() }}</h3>
                <p class="text-muted mb-0">نوبت‌های امروز</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card border-0">
            <div class="card-body text-center">
                <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
                <h3 class="mt-2 mb-1">{{ App\Models\Patient::whereMonth('created_at', now()->month)->count() }}</h3>
                <p class="text-muted mb-0">بیماران این ماه</p>
            </div>
        </div>
    </div>
</div>

<!-- جستجو و فیلتر -->
<div class="card mb-4 border-0">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="bi bi-search me-2"></i>
            جستجو و فیلتر
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('patients.index') }}" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">جستجو در نام و اطلاعات</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="نام، نام خانوادگی، شماره تلفن یا کد ملی..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">جنسیت</label>
                <select name="gender" class="form-select">
                    <option value="">همه</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>آقا</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>خانم</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">عملیات</label>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>
                        جستجو
                    </button>
                    @if(request()->hasAny(['search', 'gender']))
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        پاک کردن فیلتر
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- لیست بیماران -->
<div class="card border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-people me-2"></i>
            لیست بیماران 
            <span class="badge bg-primary">{{ number_format($patients->total()) }}</span>
        </h5>
        <div class="d-flex align-items-center">
            <small class="text-muted me-3">
                نمایش {{ $patients->firstItem() ?? 0 }} تا {{ $patients->lastItem() ?? 0 }} از {{ $patients->total() }} بیمار
            </small>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" onclick="toggleView('table')" id="tableViewBtn">
                    <i class="bi bi-table"></i>
                </button>
                <button class="btn btn-outline-secondary" onclick="toggleView('card')" id="cardViewBtn">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($patients->count() > 0)
            <!-- نمایش جدولی -->
            <div id="tableView" class="table-view">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">کد</th>
                                <th class="px-4 py-3">نام و نام خانوادگی</th>
                                <th class="px-4 py-3">شماره تماس</th>
                                <th class="px-4 py-3">جنسیت</th>
                                <th class="px-4 py-3">تاریخ ثبت</th>
                                <th class="px-4 py-3">آخرین مراجعه</th>
                                <th class="px-4 py-3 text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark">#{{ $patient->id }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('patients.show', $patient) }}" class="text-decoration-none fw-bold text-dark">
                                                {{ $patient->first_name }} {{ $patient->last_name }}
                                            </a>
                                            @if($patient->national_code)
                                                <br><small class="text-muted">کد ملی: {{ $patient->national_code }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($patient->phone)
                                        <a href="tel:{{ $patient->phone }}" class="text-decoration-none" dir="ltr">
                                            <i class="bi bi-telephone me-1"></i>
                                            {{ $patient->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($patient->gender == 'male')
                                        <span class="badge bg-info">آقا</span>
                                    @elseif($patient->gender == 'female')
                                        <span class="badge bg-warning">خانم</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">
                                        {{ \App\Helpers\PersianDateHelper::toPersian(\Carbon\Carbon::parse($patient->created_at)->format('Y-m-d')) }}
                                    </small>
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">
                                        @if($patient->appointments->count() > 0)
                                            {{ \Carbon\Carbon::parse($patient->appointments->first()->appointment_date)->diffForHumans() }}
                                        @else
                                            هنوز مراجعه‌ای نداشته
                                        @endif
                                    </small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('patients.show', $patient) }}" 
                                           class="btn btn-outline-info" 
                                           title="مشاهده پرونده">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" 
                                           class="btn btn-outline-primary" 
                                           title="ویرایش اطلاعات">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                onclick="deletePatient({{ $patient->id }})" 
                                                title="حذف بیمار">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- صفحه بندی -->
            <div class="mt-3">
                {{ $patients->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people" style="font-size: 4rem; color: #6c757d;"></i>
                <p class="mt-3 text-muted">هنوز بیماری ثبت نشده است</p>
                <a href="{{ route('patients.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-1"></i>
                    اولین بیمار را ثبت کنید
                </a>
            </div>
        @endif
    </div>
</div>

<!-- فرم حذف -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function deletePatient(id) {
    if (confirm('آیا از حذف این بیمار اطمینان دارید؟')) {
        const form = document.getElementById('delete-form');
        form.action = `/patients/${id}`;
        form.submit();
    }
}

// تعویض نمایش جدول و کارت
function toggleView(viewType) {
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const tableBtn = document.getElementById('tableViewBtn');
    const cardBtn = document.getElementById('cardViewBtn');
    
    if (viewType === 'table') {
        tableView.style.display = 'block';
        if (cardView) cardView.style.display = 'none';
        tableBtn.classList.add('active');
        cardBtn.classList.remove('active');
    } else {
        if (cardView) cardView.style.display = 'block';
        tableView.style.display = 'none';
        cardBtn.classList.add('active');
        tableBtn.classList.remove('active');
    }
    
    // ذخیره ترجیح کاربر
    localStorage.setItem('patientViewType', viewType);
}

// بازیابی ترجیح کاربر
$(document).ready(function() {
    const savedView = localStorage.getItem('patientViewType') || 'table';
    toggleView(savedView);
    
    // افکت hover برای ردیف‌ها
    $('.table tbody tr').hover(
        function() {
            $(this).addClass('table-row-hover');
        },
        function() {
            $(this).removeClass('table-row-hover');
        }
    );
});
</script>
@endpush

@push('styles')
<style>
/* استایل آواتار */
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

/* بهبود hover جدول */
.table-row-hover {
    background-color: rgba(59, 130, 246, 0.05) !important;
    transform: scale(1.002);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* استایل دکمه‌های تعویض نمایش */
.btn-group .btn.active {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* بهبود رسپانسیو */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.4rem;
    }
    
    .px-4 {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
}

/* انیمیشن لودینگ */
.loading-row {
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-card {
    animation: fadeInUp 0.5s ease-out;
}

.stats-card:nth-child(1) { animation-delay: 0.1s; }
.stats-card:nth-child(2) { animation-delay: 0.2s; }
.stats-card:nth-child(3) { animation-delay: 0.3s; }
.stats-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush
