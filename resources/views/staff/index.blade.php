@extends('layouts.app')

@section('title', 'مدیریت پرسنل')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">مدیریت پرسنل</h1>
        <p class="text-muted mb-0">مشاهده و مدیریت پرسنل کلینیک</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('staff.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            افزودن پرسنل جدید
        </a>
    </div>
</div>

<!-- Staff List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">لیست پرسنل</h5>
    </div>
    <div class="card-body">
        @if($staff->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>نام و نام خانوادگی</th>
                            <th>سمت</th>
                            <th>تلفن</th>
                            <th>حقوق ساعتی (تومان)</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $person)
                        <tr>
                            <td>{{ $person->full_name }}</td>
                            <td>
                                @if($person->role == 'doctor')
                                    <span class="badge bg-primary">دکتر</span>
                                @elseif($person->role == 'secretary')
                                    <span class="badge bg-info">منشی</span>
                                @elseif($person->role == 'assistant')
                                    <span class="badge bg-secondary">کمک‌کار</span>
                                @elseif($person->role == 'nurse')
                                    <span class="badge bg-success">پرستار</span>
                                @elseif($person->role == 'cleaner')
                                    <span class="badge bg-warning">نگهبان/تمیزکار</span>
                                @else
                                    <span class="badge bg-dark">سایر</span>
                                @endif
                            </td>
                            <td>{{ $person->phone }}</td>
                            <td>{{ number_format($person->hourly_rate) }}</td>
                            <td>
                                @if($person->is_active)
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-danger">غیرفعال</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('staff.show', $person) }}" class="btn btn-sm btn-outline-primary" title="مشاهده">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('staff.edit', $person) }}" class="btn btn-sm btn-outline-warning" title="ویرایش">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('staff.destroy', $person) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف" 
                                                onclick="return confirm('آیا از حذف این پرسنل اطمینان دارید؟')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $staff->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">هیچ پرسنلی ثبت نشده است!</h5>
                <p class="text-muted">برای شروع، پرسنل جدیدی اضافه کنید</p>
                <a href="{{ route('staff.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-person-plus me-1"></i>
                    افزودن پرسنل جدید
                </a>
            </div>
        @endif
    </div>
</div>
@endsection