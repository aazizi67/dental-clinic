@extends('layouts.app')

@section('title', 'مدیریت هزینه‌ها')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-receipt me-2"></i>
        مدیریت هزینه‌ها
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">
            <i class="bi bi-plus-circle me-1"></i>
            ثبت هزینه جدید
        </button>
    </div>
</div>

<!-- آمار سریع -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center bg-danger bg-opacity-10 border-danger">
            <div class="card-body">
                <i class="bi bi-cash-stack fs-2 text-danger mb-2"></i>
                <h5 class="card-title">کل هزینه‌های امروز</h5>
                <h3 class="text-danger">{{ number_format($todayExpenses ?? 0) }} تومان</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-warning bg-opacity-10 border-warning">
            <div class="card-body">
                <i class="bi bi-calendar-week fs-2 text-warning mb-2"></i>
                <h5 class="card-title">هزینه‌های این ماه</h5>
                <h3 class="text-warning">{{ number_format($monthExpenses ?? 0) }} تومان</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-info bg-opacity-10 border-info">
            <div class="card-body">
                <i class="bi bi-bag fs-2 text-info mb-2"></i>
                <h5 class="card-title">مواد دندانی</h5>
                <h3 class="text-info">{{ number_format($dentalMaterialsExpenses ?? 0) }} تومان</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-secondary bg-opacity-10 border-secondary">
            <div class="card-body">
                <i class="bi bi-tools fs-2 text-secondary mb-2"></i>
                <h5 class="card-title">تجهیزات</h5>
                <h3 class="text-secondary">{{ number_format($equipmentExpenses ?? 0) }} تومان</h3>
            </div>
        </div>
    </div>
</div>

<!-- فیلترها -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-funnel me-2"></i>
            فیلترهای جستجو
        </h5>
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">دسته‌بندی</label>
                <select name="category" class="form-select">
                    <option value="">همه دسته‌ها</option>
                    <option value="dental_materials" {{ request('category') == 'dental_materials' ? 'selected' : '' }}>مواد دندانی</option>
                    <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>تجهیزات</option>
                    <option value="laboratory" {{ request('category') == 'laboratory' ? 'selected' : '' }}>لابراتوار</option>
                    <option value="rent" {{ request('category') == 'rent' ? 'selected' : '' }}>اجاره</option>
                    <option value="utilities" {{ request('category') == 'utilities' ? 'selected' : '' }}>آب و برق و گاز</option>
                    <option value="marketing" {{ request('category') == 'marketing' ? 'selected' : '' }}>تبلیغات</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>سایر</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">از تاریخ</label>
                <input type="text" name="from_date" class="form-control persian-datepicker" 
                       value="{{ request('from_date') }}" placeholder="انتخاب تاریخ">
            </div>
            <div class="col-md-3">
                <label class="form-label">تا تاریخ</label>
                <input type="text" name="to_date" class="form-control persian-datepicker" 
                       value="{{ request('to_date') }}" placeholder="انتخاب تاریخ">
            </div>
            <div class="col-md-3">
                <label class="form-label">مبلغ</label>
                <div class="input-group">
                    <input type="number" name="min_amount" class="form-control" 
                           value="{{ request('min_amount') }}" placeholder="از">
                    <input type="number" name="max_amount" class="form-control" 
                           value="{{ request('max_amount') }}" placeholder="تا">
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    جستجو
                </button>
                <a href="{{ route('accounting.expenses') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    پاک کردن فیلتر
                </a>
            </div>
        </form>
    </div>
</div>

<!-- جدول هزینه‌ها -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-list-ul me-2"></i>
            لیست هزینه‌ها
        </h5>
        
        @if($expenses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>تاریخ</th>
                            <th>دسته‌بندی</th>
                            <th>شرح</th>
                            <th>مبلغ</th>
                            <th>نحوه پرداخت</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td>{{ $expense->persian_created_at }}</td>
                                <td>
                                    <span class="badge bg-{{ $expense->category_color }}">
                                        {{ $expense->category_name }}
                                    </span>
                                </td>
                                <td>{{ $expense->description }}</td>
                                <td class="text-danger fw-bold">{{ number_format($expense->amount) }} تومان</td>
                                <td>{{ $expense->payment_method_name }}</td>
                                <td>
                                    @if($expense->status == 'paid')
                                        <span class="badge bg-success">پرداخت شده</span>
                                    @elseif($expense->status == 'pending')
                                        <span class="badge bg-warning">در انتظار</span>
                                    @else
                                        <span class="badge bg-danger">لغو شده</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-sm edit-expense" 
                                                data-id="{{ $expense->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm delete-expense" 
                                                data-id="{{ $expense->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $expenses->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-receipt fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">هیچ هزینه‌ای ثبت نشده است</h5>
                <p class="text-muted">برای شروع، هزینه جدید ثبت کنید</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal ثبت/ویرایش هزینه -->
<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalTitle">ثبت هزینه جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="expenseForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="expense_id" name="expense_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">دسته‌بندی هزینه <span class="text-danger">*</span></label>
                            <select name="category" id="expense_category" class="form-select" required>
                                <option value="">انتخاب دسته‌بندی</option>
                                <option value="dental_materials">مواد دندانی</option>
                                <option value="equipment">تجهیزات</option>
                                <option value="laboratory">لابراتوار</option>
                                <option value="rent">اجاره</option>
                                <option value="utilities">آب و برق و گاز</option>
                                <option value="marketing">تبلیغات</option>
                                <option value="other">سایر</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">مبلغ (تومان) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="expense_amount" class="form-control" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">شرح هزینه <span class="text-danger">*</span></label>
                            <textarea name="description" id="expense_description" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">نحوه پرداخت <span class="text-danger">*</span></label>
                            <select name="payment_method" id="expense_payment_method" class="form-select" required>
                                <option value="">انتخاب نحوه پرداخت</option>
                                <option value="cash">نقدی</option>
                                <option value="card">کارت</option>
                                <option value="check">چک</option>
                                <option value="bank_transfer">انتقال بانکی</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">وضعیت</label>
                            <select name="status" id="expense_status" class="form-select">
                                <option value="paid">پرداخت شده</option>
                                <option value="pending">در انتظار</option>
                                <option value="cancelled">لغو شده</option>
                            </select>
                        </div>
                        
                        <!-- فیلدهای اضافی برای چک -->
                        <div id="check_fields" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">شماره چک</label>
                                    <input type="text" name="check_number" id="expense_check_number" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">تاریخ چک</label>
                                    <input type="text" name="check_date" id="expense_check_date" class="form-control persian-datepicker">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">شناسه صیاد</label>
                                    <input type="text" name="sayad_id" id="expense_sayad_id" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">یادداشت</label>
                            <textarea name="notes" id="expense_notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        ذخیره
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // مقداردهی اولیه تقویم فارسی
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
    
    // نمایش/مخفی کردن فیلدهای چک
    $('#expense_payment_method').change(function() {
        if ($(this).val() === 'check') {
            $('#check_fields').show();
            $('#expense_check_number, #expense_check_date, #expense_sayad_id').attr('required', true);
        } else {
            $('#check_fields').hide();
            $('#expense_check_number, #expense_check_date, #expense_sayad_id').removeAttr('required');
        }
    });
    
    // ارسال فرم
    $('#expenseForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const isEdit = $('#expense_id').val() !== '';
        const url = isEdit ? 
            `{{ route('accounting.expenses.update', '') }}/${$('#expense_id').val()}` : 
            `{{ route('accounting.expenses.store') }}`;
            
        if (isEdit) {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#expenseModal').modal('hide');
                    location.reload();
                } else {
                    alert('خطا در ثبت اطلاعات');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMsg = 'لطفاً خطاهای زیر را بررسی کنید:\n';
                    Object.keys(errors).forEach(key => {
                        errorMsg += `- ${errors[key][0]}\n`;
                    });
                    alert(errorMsg);
                } else {
                    alert('خطا در ثبت اطلاعات');
                }
            }
        });
    });
    
    // ویرایش هزینه
    $('.edit-expense').click(function() {
        const expenseId = $(this).data('id');
        
        $.ajax({
            url: `{{ route('accounting.expenses.show', '') }}/${expenseId}`,
            method: 'GET',
            success: function(expense) {
                $('#expenseModalTitle').text('ویرایش هزینه');
                $('#expense_id').val(expense.id);
                $('#expense_category').val(expense.category);
                $('#expense_amount').val(expense.amount);
                $('#expense_description').val(expense.description);
                $('#expense_payment_method').val(expense.payment_method).trigger('change');
                $('#expense_status').val(expense.status);
                $('#expense_check_number').val(expense.check_number);
                $('#expense_check_date').val(expense.check_date);
                $('#expense_sayad_id').val(expense.sayad_id);
                $('#expense_notes').val(expense.notes);
                
                $('#expenseModal').modal('show');
            }
        });
    });
    
    // حذف هزینه
    $('.delete-expense').click(function() {
        const expenseId = $(this).data('id');
        
        if (confirm('آیا از حذف این هزینه اطمینان دارید؟')) {
            $.ajax({
                url: `{{ route('accounting.expenses.destroy', '') }}/${expenseId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('خطا در حذف هزینه');
                    }
                }
            });
        }
    });
    
    // ریست فرم هنگام بستن مودال
    $('#expenseModal').on('hidden.bs.modal', function() {
        $('#expenseForm')[0].reset();
        $('#expense_id').val('');
        $('#expenseModalTitle').text('ثبت هزینه جدید');
        $('#check_fields').hide();
    });
});
</script>
@endpush