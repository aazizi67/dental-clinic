@extends('layouts.app')

@section('title', 'مدیریت لابراتوارها')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">مدیریت لابراتوارها</h1>
        <p class="text-muted mb-0">لیست لابراتوارهای تعریف شده</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('laboratories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            تعریف لابراتوار جدید
        </a>
    </div>
</div>

<!-- Laboratories List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">لیست لابراتوارها</h5>
    </div>
    <div class="card-body">
        @if($laboratories->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>نام لابراتوار</th>
                            <th>تلفن</th>
                            <th>آدرس</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laboratories as $laboratory)
                        <tr>
                            <td>{{ $laboratory->name }}</td>
                            <td>{{ $laboratory->phone ?? '-' }}</td>
                            <td>{{ $laboratory->address ?? '-' }}</td>
                            <td>
                                @if($laboratory->is_active)
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-danger">غیرفعال</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('laboratories.edit', $laboratory) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('laboratories.destroy', $laboratory) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این لابراتوار اطمینان دارید؟');">
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
                {{ $laboratories->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">هیچ لابراتواری تعریف نشده است!</h5>
                <p class="text-muted">برای شروع، لابراتوار جدیدی تعریف کنید</p>
                <a href="{{ route('laboratories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    تعریف لابراتوار جدید
                </a>
            </div>
        @endif
    </div>
</div>
@endsection