@extends('layouts.app')

@section('title', 'حسابداری')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">حسابداری</h1>
        <p class="text-muted mb-0">مدیریت مالی و حسابداری مطب</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('accounting.transactions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            تراکنش جدید
        </a>
    </div>
</div>

<!-- آمار مالی -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">درآمد امروز</h6>
                        <h4 class="mb-0">{{ number_format($stats['today_income']) }} ریال</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-arrow-up-circle h2 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">هزینه امروز</h6>
                        <h4 class="mb-0">{{ number_format($stats['today_expense']) }} ریال</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-arrow-down-circle h2 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">سود امروز</h6>
                        <h4 class="mb-0">{{ number_format($stats['today_profit']) }} ریال</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-graph-up h2 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">سود ماه</h6>
                        <h4 class="mb-0">{{ number_format($stats['month_profit']) }} ریال</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-month h2 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- منوی سریع -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <a href="{{ route('accounting.transactions') }}" class="card text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-list-ul h1 text-primary mb-3"></i>
                <h5>لیست تراکنش‌ها</h5>
                <p class="text-muted">مشاهده همه تراکنش‌های مالی</p>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="{{ route('accounting.transactions.create') }}" class="card text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-plus-circle h1 text-success mb-3"></i>
                <h5>تراکنش جدید</h5>
                <p class="text-muted">ثبت درآمد یا هزینه جدید</p>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="{{ route('accounting.expenses') }}" class="card text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-receipt h1 text-danger mb-3"></i>
                <h5>مدیریت هزینه‌ها</h5>
                <p class="text-muted">ثبت و پیگیری هزینه‌های مطب</p>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="{{ route('accounting.chart-of-accounts') }}" class="card text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-diagram-3 h1 text-info mb-3"></i>
                <h5>طرف حساب‌ها</h5>
                <p class="text-muted">مدیریت طرف حساب‌های مالی</p>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="{{ route('accounting.reports') }}" class="card text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-bar-chart h1 text-warning mb-3"></i>
                <h5>گزارشات</h5>
                <p class="text-muted">گزارش‌های مالی و تحلیلی</p>
            </div>
        </a>
    </div>
</div>

<!-- ردیف دوم منو -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <a href="{{ route('accounting.analytics') }}" class="card text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-graph-up h1 text-purple mb-3"></i>
                <h5>تحلیل مالی</h5>
                <p class="text-muted">تحلیل پیشرفته و نمودارها</p>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="{{ route('accounting.backup') }}" class="card text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-download h1 text-secondary mb-3"></i>
                <h5>پشتیبان و خروجی</h5>
                <p class="text-muted">تهیه نسخه پشتیبان و گزارشات</p>
            </div>
        </a>
    </div>
</div>

<!-- آخرین تراکنش‌ها -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">آخرین تراکنش‌ها</h5>
    </div>
    <div class="card-body">
        @if($recentTransactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>شماره</th>
                            <th>تاریخ</th>
                            <th>نوع</th>
                            <th>مبلغ</th>
                            <th>بیمار</th>
                            <th>روش پرداخت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_number }}</td>
                            <td>{{ $transaction->transaction_date->format('Y/m/d') }}</td>
                            <td>
                                @if($transaction->type === 'income')
                                    <span class="badge bg-success">درآمد</span>
                                @else
                                    <span class="badge bg-danger">هزینه</span>
                                @endif
                            </td>
                            <td>{{ number_format($transaction->amount) }} ریال</td>
                            <td>{{ $transaction->patient->full_name ?? '-' }}</td>
                            <td>{{ $transaction->payment_method_name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('accounting.transactions') }}" class="btn btn-outline-primary">
                    مشاهده همه تراکنش‌ها
                </a>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox h1 text-muted"></i>
                <p class="text-muted">هنوز تراکنشی ثبت نشده است</p>
                <a href="{{ route('accounting.transactions.create') }}" class="btn btn-primary">
                    ثبت اولین تراکنش
                </a>
            </div>
        @endif
    </div>
</div>
@endsection