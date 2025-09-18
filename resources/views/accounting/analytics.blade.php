@extends('layouts.app')

@section('title', 'تحلیل مالی')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-graph-up me-2"></i>
        تحلیل مالی
    </h1>
</div>

<!-- فیلتر زمانی -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">از تاریخ</label>
                <input type="text" name="from_date" class="form-control persian-datepicker" 
                       value="{{ request('from_date', now()->subDays(30)->format('Y/m/d')) }}" placeholder="انتخاب تاریخ">
            </div>
            <div class="col-md-3">
                <label class="form-label">تا تاریخ</label>
                <input type="text" name="to_date" class="form-control persian-datepicker" 
                       value="{{ request('to_date', now()->format('Y/m/d')) }}" placeholder="انتخاب تاریخ">
            </div>
            <div class="col-md-3">
                <label class="form-label">نوع تحلیل</label>
                <select name="analysis_type" class="form-select">
                    <option value="daily" {{ request('analysis_type') == 'daily' ? 'selected' : '' }}>روزانه</option>
                    <option value="weekly" {{ request('analysis_type') == 'weekly' ? 'selected' : '' }}>هفتگی</option>
                    <option value="monthly" {{ request('analysis_type') == 'monthly' ? 'selected' : '' }}>ماهانه</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    تحلیل
                </button>
            </div>
        </form>
    </div>
</div>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">کل درآمد</h6>
                        <h4 class="mb-0">{{ number_format($analytics['total_income']) }} تومان</h4>
                        <small class="opacity-75">
                            @if($analytics['income_growth'] > 0)
                                <i class="bi bi-arrow-up"></i> {{ number_format($analytics['income_growth']) }}%
                            @else
                                <i class="bi bi-arrow-down"></i> {{ number_format(abs($analytics['income_growth'])) }}%
                            @endif
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-cash-coin h2 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-gradient-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">کل هزینه</h6>
                        <h4 class="mb-0">{{ number_format($analytics['total_expenses']) }} تومان</h4>
                        <small class="opacity-75">
                            @if($analytics['expense_growth'] > 0)
                                <i class="bi bi-arrow-up"></i> {{ number_format($analytics['expense_growth']) }}%
                            @else
                                <i class="bi bi-arrow-down"></i> {{ number_format(abs($analytics['expense_growth'])) }}%
                            @endif
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-receipt h2 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-gradient-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">سود خالص</h6>
                        <h4 class="mb-0">{{ number_format($analytics['net_profit']) }} تومان</h4>
                        <small class="opacity-75">
                            @if($analytics['profit_margin'] > 0)
                                حاشیه سود: {{ number_format($analytics['profit_margin']) }}%
                            @else
                                ضرر: {{ number_format(abs($analytics['profit_margin'])) }}%
                            @endif
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-graph-up h2 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-gradient-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">میانگین روزانه</h6>
                        <h4 class="mb-0">{{ number_format($analytics['daily_average']) }} تومان</h4>
                        <small class="opacity-75">متوسط درآمد روزانه</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-day h2 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- نمودار درآمد و هزینه -->
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    روند درآمد و هزینه
                </h5>
            </div>
            <div class="card-body">
                <canvas id="revenueExpenseChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- نمودار دایره‌ای هزینه‌ها -->
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    توزیع هزینه‌ها
                </h5>
            </div>
            <div class="card-body">
                <canvas id="expenseDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- نمودار نوع درمان -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>
                    درآمد بر اساس نوع درمان
                </h5>
            </div>
            <div class="card-body">
                <canvas id="treatmentRevenueChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-credit-card me-2"></i>
                    روش‌های پرداخت
                </h5>
            </div>
            <div class="card-body">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- جداول تحلیلی -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-trophy me-2"></i>
                    پردرآمدترین روزها
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>درآمد</th>
                                <th>تعداد بیمار</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analytics['top_revenue_days'] as $day)
                            <tr>
                                <td>{{ $day['date'] }}</td>
                                <td class="text-success fw-bold">{{ number_format($day['revenue']) }} تومان</td>
                                <td>{{ $day['patient_count'] }} نفر</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    پرهزینه‌ترین دسته‌ها
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>دسته‌بندی</th>
                                <th>مبلغ</th>
                                <th>درصد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analytics['top_expense_categories'] as $category)
                            <tr>
                                <td>{{ $category['name'] }}</td>
                                <td class="text-danger fw-bold">{{ number_format($category['amount']) }} تومان</td>
                                <td>{{ number_format($category['percentage']) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // تقویم فارسی
    $('.persian-datepicker').each(function() {
        $(this).pDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            calendar: {
                persian: {
                    locale: 'fa'
                }
            }
        });
    });
    
    // نمودار درآمد و هزینه
    const revenueExpenseCtx = document.getElementById('revenueExpenseChart').getContext('2d');
    new Chart(revenueExpenseCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($analytics['chart_labels']) !!},
            datasets: [{
                label: 'درآمد',
                data: {!! json_encode($analytics['revenue_data']) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'هزینه',
                data: {!! json_encode($analytics['expense_data']) !!},
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fa-IR').format(value) + ' تومان';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   new Intl.NumberFormat('fa-IR').format(context.parsed.y) + ' تومان';
                        }
                    }
                }
            }
        }
    });
    
    // نمودار توزیع هزینه‌ها
    const expenseDistCtx = document.getElementById('expenseDistributionChart').getContext('2d');
    new Chart(expenseDistCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($analytics['expense_categories']) !!},
            datasets: [{
                data: {!! json_encode($analytics['expense_amounts']) !!},
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', 
                    '#17a2b8', '#6c757d', '#343a40'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + 
                                   new Intl.NumberFormat('fa-IR').format(context.parsed) + ' تومان';
                        }
                    }
                }
            }
        }
    });
    
    // نمودار درآمد بر اساس نوع درمان
    const treatmentRevenueCtx = document.getElementById('treatmentRevenueChart').getContext('2d');
    new Chart(treatmentRevenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($analytics['treatment_types']) !!},
            datasets: [{
                label: 'درآمد',
                data: {!! json_encode($analytics['treatment_revenues']) !!},
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fa-IR').format(value) + ' تومان';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'درآمد: ' + new Intl.NumberFormat('fa-IR').format(context.parsed.y) + ' تومان';
                        }
                    }
                }
            }
        }
    });
    
    // نمودار روش‌های پرداخت
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentMethodCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($analytics['payment_methods']) !!},
            datasets: [{
                data: {!! json_encode($analytics['payment_amounts']) !!},
                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + 
                                   new Intl.NumberFormat('fa-IR').format(context.parsed) + ' تومان';
                        }
                    }
                }
            }
        }
    });
});
</script>

<style>
.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #20c997);
}
.bg-gradient-danger {
    background: linear-gradient(45deg, #dc3545, #fd7e14);
}
.bg-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #6f42c1);
}
.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #fd7e14);
}
</style>
@endpush