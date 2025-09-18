@extends('layouts.app')

@section('title', 'ثبت معاینه')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">ثبت معاینه</h1>
        <p class="text-muted mb-0">مدیریت و ثبت معاینات و طرح درمان بیماران</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('treatments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            معاینه جدید
        </a>
    </div>
</div>

<!-- فیلتر جستجو -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('treatments.index') }}" class="row g-3">
            <div class="col-md-6">
                <label for="patient_search" class="form-label">جستجوی بیمار</label>
                <input type="text" class="form-control" id="patient_search" name="patient_search" 
                       value="{{ request('patient_search') }}" placeholder="نام، نام خانوادگی یا شماره تماس">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">وضعیت</label>
                <select class="form-select" id="status" name="status">
                    <option value="">همه</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>پیش‌نویس</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search me-1"></i>
                    جستجو
                </button>
                <a href="{{ route('treatments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    پاک کردن
                </a>
            </div>
        </form>
    </div>
</div>

<!-- لیست معاینات -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">لیست معاینات ثبت شده</h5>
    </div>
    <div class="card-body">
        @if($treatmentPlans->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>بیمار</th>
                            <th>دکتر</th>
                            <th>عنوان</th>
                            <th>هزینه کل</th>
                            <th>پرداخت شده</th>
                            <th>وضعیت</th>
                            <th>تاریخ ایجاد</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($treatmentPlans as $plan)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            {{ substr($plan->patient->first_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $plan->patient->full_name }}</div>
                                        <small class="text-muted">{{ $plan->patient->phone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $plan->doctor->name }}</td>
                            <td>{{ $plan->title }}</td>
                            <td>{{ number_format($plan->total_cost) }} ریال</td>
                            <td>{{ number_format($plan->paid_amount) }} ریال</td>
                            <td>
                                @switch($plan->status)
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
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($plan->created_at)->format('Y/m/d H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('treatments.show', $plan) }}" class="btn btn-outline-primary" title="مشاهده">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('treatments.edit', $plan) }}" class="btn btn-outline-warning" title="ویرایش">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="حذف" 
                                            onclick="confirmDelete({{ $plan->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- صفحه‌بندی -->
            <div class="d-flex justify-content-center mt-4">
                {{ $treatmentPlans->withQueryString()->links() }}
            </div>
        @else
            <!-- حالت خالی -->
            <div class="text-center py-5">
                <i class="bi bi-clipboard2-check text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">هیچ معاینه‌ای ثبت نشده است</h5>
                <p class="text-muted">برای شروع، معاینه جدیدی ایجاد کنید</p>
                <a href="{{ route('treatments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    معاینه جدید
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal تأیید حذف -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأیید حذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                آیا از حذف این معاینه اطمینان دارید؟ این عمل قابل برگشت نیست.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(planId) {
    document.getElementById('deleteForm').action = `/treatments/${planId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush