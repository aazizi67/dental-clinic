@extends('layouts.app')

@section('title', 'طرف حساب‌ها')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">طرف حساب‌ها</h1>
        <p class="text-muted mb-0">مدیریت طرف حساب‌های مالی مطب</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
            <i class="bi bi-plus-lg me-1"></i>
            حساب جدید
        </button>
    </div>
</div>

<div class="row">
    <!-- طرف حساب‌ها -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">ساختار حساب‌ها</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>کد</th>
                                <th>نام حساب</th>
                                <th>نوع</th>
                                <th>مانده</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts->where('level', 1) as $mainAccount)
                                <tr class="table-primary">
                                    <td><strong>{{ $mainAccount->code }}</strong></td>
                                    <td><strong>{{ $mainAccount->name }}</strong></td>
                                    <td>
                                        @switch($mainAccount->type)
                                            @case('asset')
                                                <span class="badge bg-success">داراییی</span>
                                                @break
                                            @case('liability')
                                                <span class="badge bg-warning">بدهی</span>
                                                @break
                                            @case('equity')
                                                <span class="badge bg-info">حقوق صاحبان</span>
                                                @break
                                            @case('income')
                                                <span class="badge bg-primary">درآمد</span>
                                                @break
                                            @case('expense')
                                                <span class="badge bg-danger">هزینه</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <strong>{{ number_format($mainAccount->calculateBalance()) }} ریال</strong>
                                    </td>
                                    <td>
                                        @if($mainAccount->is_active)
                                            <span class="badge bg-success">فعال</span>
                                        @else
                                            <span class="badge bg-secondary">غیرفعال</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editAccount({{ $mainAccount->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- حساب‌های فرعی -->
                                @foreach($mainAccount->children as $subAccount)
                                    <tr>
                                        <td style="padding-right: 30px;">{{ $subAccount->code }}</td>
                                        <td style="padding-right: 30px;">{{ $subAccount->name }}</td>
                                        <td>
                                            @switch($subAccount->type)
                                                @case('asset')
                                                    <span class="badge bg-success">داراییی</span>
                                                    @break
                                                @case('liability')
                                                    <span class="badge bg-warning">بدهی</span>
                                                    @break
                                                @case('equity')
                                                    <span class="badge bg-info">حقوق صاحبان</span>
                                                    @break
                                                @case('income')
                                                    <span class="badge bg-primary">درآمد</span>
                                                    @break
                                                @case('expense')
                                                    <span class="badge bg-danger">هزینه</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ number_format($subAccount->calculateBalance()) }} ریال</td>
                                        <td>
                                            @if($subAccount->is_active)
                                                <span class="badge bg-success">فعال</span>
                                            @else
                                                <span class="badge bg-secondary">غیرفعال</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="editAccount({{ $subAccount->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-info" onclick="viewTransactions({{ $subAccount->id }})">
                                                    <i class="bi bi-list-ul"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- خلاصه مالی -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">خلاصه مالی</h6>
            </div>
            <div class="card-body">
                @php
                    $totalAssets = $accounts->where('type', 'asset')->sum(function($account) { 
                        return $account->calculateBalance(); 
                    });
                    $totalIncome = $accounts->where('type', 'income')->sum(function($account) { 
                        return $account->calculateBalance(); 
                    });
                    $totalExpense = $accounts->where('type', 'expense')->sum(function($account) { 
                        return $account->calculateBalance(); 
                    });
                    $netProfit = $totalIncome - $totalExpense;
                @endphp
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>کل داراییی‌ها:</span>
                        <strong class="text-success">{{ number_format($totalAssets) }} ریال</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>کل درآمدها:</span>
                        <strong class="text-primary">{{ number_format($totalIncome) }} ریال</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>کل هزینه‌ها:</span>
                        <strong class="text-danger">{{ number_format($totalExpense) }} ریال</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>سود خالص:</span>
                        <strong class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($netProfit) }} ریال
                        </strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">راهنمای حساب‌ها</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-success">داراییی‌ها</h6>
                    <p class="small text-muted">نقدینگی، تجهیزات، چک‌های دریافتی</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-primary">درآمدها</h6>
                    <p class="small text-muted">درآمد از خدمات درمانی بیماران</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-danger">هزینه‌ها</h6>
                    <p class="small text-muted">مواد دندانی، تجهیزات، لابراتوار</p>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>نکته:</strong> تغییرات در طرف حساب‌ها روی گزارش‌های مالی تأثیر می‌گذارد.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal افزودن حساب جدید -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حساب جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addAccountForm">
                    @csrf
                    <div class="mb-3">
                        <label for="code" class="form-label">کد حساب <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required>
                        <div class="form-text">کد یکتا برای حساب (مثال: 1150)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">نام حساب <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">نوع حساب <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">انتخاب کنید</option>
                            <option value="asset">داراییی</option>
                            <option value="liability">بدهی</option>
                            <option value="equity">حقوق صاحبان</option>
                            <option value="income">درآمد</option>
                            <option value="expense">هزینه</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">حساب والد</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">حساب اصلی</option>
                            @foreach($accounts->where('level', 1) as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">توضیحات</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">
                            حساب فعال
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                <button type="button" class="btn btn-primary" onclick="saveAccount()">ذخیره</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editAccount(accountId) {
    // ویرایش حساب (در نسخه‌های بعدی پیاده‌سازی می‌شود)
    alert('ویرایش حساب: ' + accountId);
}

function viewTransactions(accountId) {
    // نمایش تراکنش‌های مربوط به حساب
    window.location.href = `{{ route('accounting.transactions') }}?account_id=${accountId}`;
}

function saveAccount() {
    // ذخیره حساب جدید (در نسخه‌های بعدی با AJAX پیاده‌سازی می‌شود)
    alert('قابلیت افزودن حساب در نسخه بعدی اضافه خواهد شد');
}
</script>
@endpush