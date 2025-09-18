@extends('layouts.app')

@section('title', 'تراکنش‌های لابراتوار')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">تراکنش‌های لابراتوار</h1>
        <p class="text-muted mb-0">ثبت ورود و خروج کارهای لابراتوار</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('laboratories.transactions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            ثبت تراکنش جدید
        </a>
        <a href="{{ route('laboratories.reports') }}" class="btn btn-outline-success ms-2">
            <i class="bi bi-file-text me-1"></i>
            گزارش‌ها
        </a>
    </div>
</div>

<!-- Transactions List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">لیست تراکنش‌ها</h5>
    </div>
    <div class="card-body">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>ساعت</th>
                            <th>لابراتوار</th>
                            <th>بیمار</th>
                            <th>دندانپزشک</th>
                            <th>نوع</th>
                            <th>دسته‌بندی</th>
                            <th>قیمت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ verta($transaction->date)->format('Y/m/d') }}</td>
                            <td>{{ $transaction->time }}</td>
                            <td>{{ $transaction->laboratory->name }}</td>
                            <td>{{ $transaction->patient->full_name }}</td>
                            <td>{{ $transaction->doctor->name ?? '-' }}</td>
                            <td>
                                @if($transaction->type == 'entry')
                                    <span class="badge bg-warning">خروجی (ارسال به لابراتوار)</span>
                                @else
                                    <span class="badge bg-success">ورودی (دریافت از لابراتوار)</span>
                                @endif
                            </td>
                            <td>
                                @switch($transaction->category)
                                    @case('post')
                                        پست
                                        @break
                                    @case('crown')
                                        روکش
                                        @break
                                    @case('laminat')
                                        لمینت
                                        @break
                                    @case('implant_crown')
                                        روکش ایمپلنت
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $transaction->price ? number_format($transaction->price, 0) . ' تومان' : '-' }}</td>
                            <td>
                                <a href="{{ route('laboratories.transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('laboratories.transactions.destroy', $transaction) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این تراکنش اطمینان دارید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-clipboard-data text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">هیچ تراکنشی ثبت نشده است!</h5>
                <p class="text-muted">برای شروع، تراکنش جدیدی ثبت کنید</p>
                <a href="{{ route('laboratories.transactions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    ثبت تراکنش جدید
                </a>
            </div>
        @endif
    </div>
</div>
@endsection