@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>لیست نوبت‌ها</h2>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card mt-4">
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>تاریخ</th>
                                        <th>ساعت</th>
                                        <th>نام بیمار</th>
                                        <th>شماره تماس</th>
                                        <th>وضعیت</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td>{{ Verta::instance($appointment->appointment_date)->format('Y/m/d') }}</td>
                                            <td>{{ $appointment->appointment_time }}</td>
                                            <td>{{ $appointment->patient_name }}</td>
                                            <td>{{ $appointment->patient_phone }}</td>
                                            <td>
                                                @if($appointment->status == 'scheduled')
                                                    <span class="badge bg-primary">زمانبندی شده</span>
                                                @elseif($appointment->status == 'confirmed')
                                                    <span class="badge bg-success">تایید شده</span>
                                                @elseif($appointment->status == 'cancelled')
                                                    <span class="badge bg-danger">لغو شده</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-sm btn-info">مشاهده</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">هیچ نوبتی ثبت نشده است.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection