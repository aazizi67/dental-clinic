@extends('layouts.app')

@section('title', 'گزارشات مالی')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">گزارشات مالی</h1>
        <p class="text-muted mb-0">تحلیل‌های مالی و گزارش‌های حسابداری</p>
    </div>
</div>

<!-- فیلتر بازه زمانی -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('accounting.reports') }}" class="row g-3">
            <div class="col-md-4">
                <label for="date_from" class="form-label">از تاریخ</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="{{ $dateFrom }}">
            </div>
            
            <div class="col-md-4">
                <label for="date_to" class="form-label">تا تاریخ</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="{{ $dateTo }}">
            </div>
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>مشاهده گزارش
                </button>
                <button type="button" class="btn btn-outline-success" onclick="exportReport()">
                    <i class="bi bi-download me-1"></i>خروجی
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- گزارش درآمد و هزینه -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">گزارش درآمد و هزینه</h5>
            </div>
            <div class="card-body">
                <h6 class="text-success">درآمدها</h6>
                @php $totalIncome = 0; @endphp
                @foreach($incomeByCategory as $income)
                    @php $totalIncome += $income->total; @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            @switch($income->category)
                                @case('patient_payment')
                                    پرداخت بیماران
                                    @break
                                @default
                                    {{ $income->category }}
                            @endswitch
                        </span>
                        <strong class="text-success">{{ number_format($income->total) }} ریال</strong>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>کل درآمد:</strong>
                    <strong class="text-success">{{ number_format($totalIncome) }} ریال</strong>
                </div>
                
                <h6 class="text-danger mt-4">هزینه‌ها</h6>
                @php $totalExpense = 0; @endphp
                @foreach($expenseByCategory as $expense)
                    @php $totalExpense += $expense->total; @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            @switch($expense->category)
                                @case('dental_materials')
                                    مواد دندانی
                                    @break
                                @case('equipment')
                                    تجهیزات
                                    @break
                                @case('laboratory')
                                    لابراتوار
                                    @break
                                @case('other')
                                    سایر
                                    @break
                                @default
                                    {{ $expense->category }}
                            @endswitch
                        </span>
                        <strong class="text-danger">{{ number_format($expense->total) }} ریال</strong>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>کل هزینه:</strong>
                    <strong class="text-danger">{{ number_format($totalExpense) }} ریال</strong>
                </div>
                
                <hr class="mt-4">
                @php $netProfit = $totalIncome - $totalExpense; @endphp
                <div class="d-flex justify-content-between">
                    <strong>سود خالص:</strong>
                    <strong class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($netProfit) }} ریال
                    </strong>
                </div>
            </div>
        </div>
    </div>
    
    <!-- نمودار درآمد و هزینه -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">نمودار مقایسه‌ای</h5>
            </div>
            <div class="card-body">
                <canvas id="incomeExpenseChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- گزارش روزانه -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">گزارش روزانه</h5>
    </div>
    <div class="card-body">
        @if($dailyReport->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>درآمد روز</th>
                            <th>هزینه روز</th>
                            <th>سود/زیان روز</th>
                            <th>درصد سود</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dailyData = $dailyReport->groupBy('transaction_date');
                        @endphp
                        @foreach($dailyData as $date => $transactions)
                            @php
                                $dayIncome = $transactions->where('type', 'income')->sum('total');
                                $dayExpense = $transactions->where('type', 'expense')->sum('total');
                                $dayProfit = $dayIncome - $dayExpense;
                                $profitPercent = $dayIncome > 0 ? ($dayProfit / $dayIncome) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}</td>
                                <td class="text-success">{{ number_format($dayIncome) }} ریال</td>
                                <td class="text-danger">{{ number_format($dayExpense) }} ریال</td>
                                <td class="{{ $dayProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($dayProfit) }} ریال
                                </td>
                                <td class="{{ $profitPercent >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($profitPercent, 1) }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-bar-chart h1 text-muted"></i>
                <p class="text-muted">برای بازه زمانی انتخاب شده داده‌ای یافت نشد</p>
            </div>
        @endif
    </div>
</div>

<!-- آمار کلی -->
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($totalIncome) }}</h4>
                <p class="mb-0">کل درآمد (ریال)</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($totalExpense) }}</h4>
                <p class="mb-0">کل هزینه (ریال)</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-{{ $netProfit >= 0 ? 'success' : 'warning' }} text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($netProfit) }}</h4>
                <p class="mb-0">سود خالص (ریال)</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $totalIncome > 0 ? number_format(($netProfit / $totalIncome) * 100, 1) : 0 }}%</h4>
                <p class="mb-0">درصد سود</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // نمودار درآمد و هزینه
    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    
    const incomeData = @json($incomeByCategory);
    const expenseData = @json($expenseByCategory);
    
    const incomeLabels = incomeData.map(item => {
        switch(item.category) {
            case 'patient_payment': return 'پرداخت بیماران';
            default: return item.category;
        }
    });
    
    const expenseLabels = expenseData.map(item => {
        switch(item.category) {
            case 'dental_materials': return 'مواد دندانی';
            case 'equipment': return 'تجهیزات';
            case 'laboratory': return 'لابراتوار';
            case 'other': return 'سایر';
            default: return item.category;
        }
    });
    
    const incomeValues = incomeData.map(item => item.total);
    const expenseValues = expenseData.map(item => item.total);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [...incomeLabels, ...expenseLabels],
            datasets: [
                {
                    label: 'درآمد',
                    data: [...incomeValues, ...Array(expenseLabels.length).fill(0)],
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'هزینه',
                    data: [...Array(incomeLabels.length).fill(0), ...expenseValues],
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ریال';
                        }
                    }
                }
            }
        }
    });
});

function exportReport() {
    // خروجی گزارش (در نسخه‌های بعدی پیاده‌سازی می‌شود)
    alert('قابلیت خروجی گزارش در نسخه بعدی اضافه خواهد شد');
}
</script>
@endpush