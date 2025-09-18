@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">دریافت نوبت معاینه اولیه</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('appointments.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="patient_name" class="form-label">نام و نام خانوادگی</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="patient_phone" class="form-label">شماره تماس</label>
                            <input type="text" class="form-control" id="patient_phone" name="patient_phone" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="appointment_date" class="form-label">تاریخ مراجعه</label>
                            <select class="form-select" id="appointment_date" name="appointment_date" required>
                                <option value="">انتخاب تاریخ</option>
                                @foreach($dates as $gregorian => $persian)
                                    <option value="{{ $gregorian }}">{{ $persian }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="appointment_time" class="form-label">ساعت مراجعه</label>
                            <select class="form-select" id="appointment_time" name="appointment_time" required>
                                <option value="">انتخاب ساعت</option>
                                @foreach($timeSlots as $slot)
                                    <option value="{{ $slot->start_time }}">{{ $slot->start_time }} - {{ $slot->end_time }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">توضیحات (اختیاری)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">ثبت نوبت</button>
                        <a href="{{ route('appointments.home') }}" class="btn btn-secondary">بازگشت</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection