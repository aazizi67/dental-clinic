@extends('layouts.app')

@section('title', 'لیست تراکنش‌ها')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">لیست تراکنش‌ها</h1>
        <p class="text-muted mb-0">مدیریت کلیه تراکنش‌های مالی</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('accounting.transactions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            تراکنش جدید
        </a>
    </div>
</div>

<!-- فیلترهای جستجو -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('accounting.transactions') }}" class="row g-3">
            <div class="col-md-3">
                <label for="date_from" class="form-label">از تاریخ</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-3">
                <label for="date_to" class="form-label">تا تاریخ</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-2">
                <label for="type" class="form-label">نوع</label>
                <select class="form-select" id="type" name="type">
                    <option value="">همه</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>درآمد</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>هزینه</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="category" class="form-label">دسته‌بندی</label>
                <select class="form-select" id="category" name="category">
                    <option value="">همه</option>
                    <option value="patient_payment" {{ request('category') == 'patient_payment' ? 'selected' : '' }}>پرداخت بیمار</option>
                    <option value="dental_materials" {{ request('category') == 'dental_materials' ? 'selected' : '' }}>مواد دندانی</option>
                    <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>تجهیزات</option>
                    <option value="laboratory" {{ request('category') == 'laboratory' ? 'selected' : '' }}>لابراتوار</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>سایر</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search me-1"></i>جستجو
                </button>
                <a href="{{ route('accounting.transactions') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- جدول تراکنش‌ها -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">تراکنش‌ها</h5>
    </div>
    <div class="card-body">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>شماره تراکنش</th>
                            <th>تاریخ</th>
                            <th>نوع</th>
                            <th>دسته‌بندی</th>
                            <th>مبلغ</th>
                            <th>بیمار</th>
                            <th>روش پرداخت</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>
                                <strong>{{ $transaction->transaction_number }}</strong>
                            </td>
                            <td>{{ $transaction->transaction_date->format('Y/m/d') }}</td>
                            <td>
                                @if($transaction->type === 'income')
                                    <span class="badge bg-success">درآمد</span>
                                @else
                                    <span class="badge bg-danger">هزینه</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $transaction->category_name }}</span>
                            </td>
                            <td>
                                <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($transaction->amount) }} ریال
                                </strong>
                            </td>
                            <td>
                                @if($transaction->patient)
                                    <div>
                                        <strong>{{ $transaction->patient->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $transaction->patient->phone }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $transaction->payment_method_name }}</span>
                                @if($transaction->payment_method === 'check')
                                    <br>
                                    <small class="text-muted">{{ $transaction->check_number }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($transaction->status)
                                    @case('completed')
                                        <span class="badge bg-success">تکمیل شده</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning">در انتظار</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">لغو شده</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">نامشخص</span>
                                @endswitch
                                
                                @if($transaction->payment_method === 'check' && $transaction->check_status)
                                    <br>
                                    @switch($transaction->check_status)
                                        @case('received')
                                            <small class="badge bg-info">دریافت شده</small>
                                            @break
                                        @case('deposited')
                                            <small class="badge bg-primary">سپرده شده</small>
                                            @break
                                        @case('cleared')
                                            <small class="badge bg-success">تسویه شده</small>
                                            @break
                                        @case('bounced')
                                            <small class="badge bg-danger">برگشتی</small>
                                            @break
                                    @endswitch
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" onclick="showTransactionDetails({{ $transaction->id }})" 
                                            title="جزئیات">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($transaction->payment_method === 'check')
                                        <button class="btn btn-outline-warning" onclick="manageCheck({{ $transaction->id }})" 
                                                title="مدیریت چک">
                                            <i class="bi bi-journal-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="4">مجموع:</th>
                            <th>
                                @php
                                    $totalIncome = $transactions->where('type', 'income')->sum('amount');
                                    $totalExpense = $transactions->where('type', 'expense')->sum('amount');
                                    $netAmount = $totalIncome - $totalExpense;
                                @endphp
                                <div class="text-success">درآمد: {{ number_format($totalIncome) }} ریال</div>
                                <div class="text-danger">هزینه: {{ number_format($totalExpense) }} ریال</div>
                                <div class="fw-bold {{ $netAmount >= 0 ? 'text-success' : 'text-danger' }}">
                                    خالص: {{ number_format($netAmount) }} ریال
                                </div>
                            </th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox h1 text-muted"></i>
                <h5 class="text-muted">تراکنشی یافت نشد</h5>
                <p class="text-muted">هیچ تراکنشی برای فیلترهای انتخاب شده یافت نشد</p>
                <a href="{{ route('accounting.transactions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    ثبت اولین تراکنش
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal برای نمایش جزئیات تراکنش -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">جزئیات تراکنش</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionDetailsContent">
                <!-- محتوا با AJAX بارگذاری می‌شود -->
            </div>
        </div>
    </div>
</div>

<!-- Modal برای مدیریت چک -->
<div class="modal fade" id="checkManagementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مدیریت چک</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="checkManagementContent">
                <!-- محتوا با AJAX بارگذاری می‌شود -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTransactionDetails(transactionId) {
    // نمایش جزئیات تراکنش (در نسخه‌های بعدی پیاده‌سازی می‌شود)
    alert('نمایش جزئیات تراکنش: ' + transactionId);
}

function manageCheck(transactionId) {
    // مدیریت وضعیت چک (در نسخه‌های بعدی پیاده‌سازی می‌شود)
    alert('مدیریت چک: ' + transactionId);
}

// فرمت کردن مبالغ در جدول
$(document).ready(function() {
    $('.amount').each(function() {
        const amount = parseInt($(this).text());
        $(this).text(amount.toLocaleString());
    });
});
</script>
@endpush