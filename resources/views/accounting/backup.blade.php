@extends('layouts.app')

@section('title', 'تهیه نسخه پشتیبان و خروجی')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-download me-2"></i>
        تهیه نسخه پشتیبان و خروجی
    </h1>
</div>

<div class="row">
    <!-- خروجی گزارشات -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                    خروجی گزارشات
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">دانلود گزارشات مالی در فرمت‌های مختلف</p>
                
                <form id="exportForm" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نوع گزارش</label>
                            <select name="report_type" class="form-select" required>
                                <option value="">انتخاب نوع گزارش</option>
                                <option value="transactions">لیست تراکنش‌ها</option>
                                <option value="expenses">هزینه‌ها</option>
                                <option value="income_expense">درآمد و هزینه</option>
                                <option value="profit_loss">سود و زیان</option>
                                <option value="balance_sheet">ترازنامه</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">فرمت خروجی</label>
                            <select name="format" class="form-select" required>
                                <option value="">انتخاب فرمت</option>
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="pdf">PDF</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">از تاریخ</label>
                            <input type="text" name="from_date" class="form-control persian-datepicker" placeholder="انتخاب تاریخ">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">تا تاریخ</label>
                            <input type="text" name="to_date" class="form-control persian-datepicker" placeholder="انتخاب تاریخ">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-download me-1"></i>
                            دانلود گزارش
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- نسخه پشتیبان -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>
                    نسخه پشتیبان
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">تهیه نسخه کامل از اطلاعات مالی</p>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    نسخه پشتیبان شامل تمامی اطلاعات حسابداری، تراکنش‌ها و هزینه‌ها می‌باشد.
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-success" id="fullBackupBtn">
                        <i class="bi bi-database me-1"></i>
                        تهیه نسخه کامل
                    </button>
                    
                    <button class="btn btn-outline-success" id="accountingBackupBtn">
                        <i class="bi bi-calculator me-1"></i>
                        فقط اطلاعات حسابداری
                    </button>
                </div>
                
                <hr>
                
                <h6>آخرین نسخه‌های پشتیبان:</h6>
                <div class="list-group list-group-flush">
                    @forelse($backups ?? [] as $backup)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $backup['name'] }}</strong>
                            <br>
                            <small class="text-muted">{{ $backup['date'] }} - {{ $backup['size'] }}</small>
                        </div>
                        <div>
                            <a href="{{ $backup['download_url'] }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-inbox"></i>
                        <p>هنوز نسخه پشتیبانی تهیه نشده است</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- آمار سریع -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center bg-primary bg-opacity-10 border-primary">
            <div class="card-body">
                <i class="bi bi-list-ul fs-2 text-primary mb-2"></i>
                <h5 class="card-title">کل تراکنش‌ها</h5>
                <h3 class="text-primary">{{ $stats['total_transactions'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center bg-danger bg-opacity-10 border-danger">
            <div class="card-body">
                <i class="bi bi-receipt fs-2 text-danger mb-2"></i>
                <h5 class="card-title">کل هزینه‌ها</h5>
                <h3 class="text-danger">{{ $stats['total_expenses'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center bg-success bg-opacity-10 border-success">
            <div class="card-body">
                <i class="bi bi-people fs-2 text-success mb-2"></i>
                <h5 class="card-title">کل بیماران</h5>
                <h3 class="text-success">{{ $stats['total_patients'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center bg-info bg-opacity-10 border-info">
            <div class="card-body">
                <i class="bi bi-diagram-3 fs-2 text-info mb-2"></i>
                <h5 class="card-title">حساب‌های مالی</h5>
                <h3 class="text-info">{{ $stats['total_accounts'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- راهنمای کاربری -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-question-circle me-2"></i>
            راهنما
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="bi bi-file-earmark-spreadsheet me-2"></i>انواع گزارشات:</h6>
                <ul class="list-unstyled ms-3">
                    <li><strong>لیست تراکنش‌ها:</strong> همه تراکنش‌های مالی</li>
                    <li><strong>هزینه‌ها:</strong> جزئیات کلیه هزینه‌ها</li>
                    <li><strong>درآمد و هزینه:</strong> مقایسه درآمد و هزینه</li>
                    <li><strong>سود و زیان:</strong> گزارش سود و زیان</li>
                    <li><strong>ترازنامه:</strong> وضعیت مالی کلی</li>
                </ul>
            </div>
            
            <div class="col-md-6">
                <h6><i class="bi bi-shield-check me-2"></i>نکات نسخه پشتیبان:</h6>
                <ul class="list-unstyled ms-3">
                    <li>• نسخه کامل شامل همه اطلاعات سیستم</li>
                    <li>• نسخه حسابداری فقط اطلاعات مالی</li>
                    <li>• فایل‌ها در فرمت ZIP ذخیره می‌شوند</li>
                    <li>• توصیه: ماهانه نسخه پشتیبان تهیه کنید</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
    
    // خروجی گزارشات
    $('#exportForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>در حال پردازش...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("accounting.export") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                // دریافت نام فایل از header
                const filename = xhr.getResponseHeader('Content-Disposition')
                    ?.split('filename=')[1]?.replace(/"/g, '') || 'report.xlsx';
                
                // ایجاد لینک دانلود
                const blob = new Blob([data]);
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                
                // نمایش پیام موفقیت
                showAlert('success', 'گزارش با موفقیت دانلود شد');\n            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'خطا در تهیه گزارش';\n                showAlert('danger', errorMsg);
            },
            complete: function() {
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    // نسخه پشتیبان کامل
    $('#fullBackupBtn').click(function() {
        createBackup('full', $(this));
    });
    
    // نسخه پشتیبان حسابداری
    $('#accountingBackupBtn').click(function() {
        createBackup('accounting', $(this));
    });
    
    function createBackup(type, btn) {
        const originalText = btn.html();
        btn.html('<i class="spinner-border spinner-border-sm me-1"></i>در حال تهیه...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("accounting.backup") }}',
            method: 'POST',
            data: {
                type: type,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'نسخه پشتیبان با موفقیت تهیه شد');
                    
                    // دانلود خودکار فایل
                    if (response.download_url) {
                        const a = document.createElement('a');
                        a.href = response.download_url;
                        a.download = response.filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    }
                    
                    // بروزرسانی لیست نسخه‌های پشتیبان
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert('danger', 'خطا در تهیه نسخه پشتیبان');
                }
            },
            error: function() {
                showAlert('danger', 'خطا در تهیه نسخه پشتیبان');
            },
            complete: function() {
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    }
    
    function showAlert(type, message) {
        const alert = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container-fluid .row .col-md-9').prepend(alert);
        
        // حذف خودکار پس از 5 ثانیه
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush