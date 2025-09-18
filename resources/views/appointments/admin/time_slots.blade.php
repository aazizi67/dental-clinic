@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>مدیریت بازه‌های زمانی</h2>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">افزودن بازه زمانی جدید</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.time-slots.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">تاریخ</label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">ساعت شروع</label>
                                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">ساعت پایان</label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block">افزودن</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">لیست بازه‌های زمانی</h5>
                </div>
                <div class="card-body">
                    @if($timeSlots->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>تاریخ</th>
                                        <th>ساعت شروع</th>
                                        <th>ساعت پایان</th>
                                        <th>وضعیت</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timeSlots as $slot)
                                        <tr>
                                            <td>{{ Verta::instance($slot->date)->format('Y/m/d') }}</td>
                                            <td>{{ $slot->start_time }}</td>
                                            <td>{{ $slot->end_time }}</td>
                                            <td>
                                                @if($slot->is_available)
                                                    <span class="badge bg-success">فعال</span>
                                                @else
                                                    <span class="badge bg-secondary">غیرفعال</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.time-slots.destroy', $slot->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این بازه زمانی اطمینان دارید؟')">حذف</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">هیچ بازه زمانی تعریف نشده است.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection