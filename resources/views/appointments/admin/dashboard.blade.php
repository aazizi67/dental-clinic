@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>پنل مدیریت نوبت دهی</h2>
            
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">مدیریت بازه‌های زمانی</h5>
                            <p class="card-text">تعریف و مدیریت بازه‌های زمانی قابل رزرو</p>
                            <a href="{{ route('admin.time-slots.index') }}" class="btn btn-primary">مشاهده</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">لیست نوبت‌ها</h5>
                            <p class="card-text">مشاهده و مدیریت تمام نوبت‌های ثبت شده</p>
                            <a href="{{ route('admin.appointments.index') }}" class="btn btn-primary">مشاهده</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">گزارش‌ها</h5>
                            <p class="card-text">مشاهده گزارش‌های مربوط به نوبت‌دهی</p>
                            <a href="#" class="btn btn-primary disabled">مشاهده</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection