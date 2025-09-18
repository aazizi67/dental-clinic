@extends('layouts.app')

@section('title', 'مشاهده بیمار')

@section('content')
<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">پرونده بیمار</h1>
    <div>
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>ویرایش
        </a>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>بازگشت
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">اطلاعات شخصی</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">نام:</dt>
                    <dd class="col-sm-8">{{ $patient->first_name }} {{ $patient->last_name }}</dd>
                    
                    <dt class="col-sm-4">شماره تماس:</dt>
                    <dd class="col-sm-8">{{ $patient->phone }}</dd>
                    
                    <dt class="col-sm-4">کد ملی:</dt>
                    <dd class="col-sm-8">{{ $patient->national_code ?? '-' }}</dd>
                    
                    <dt class="col-sm-4">تاریخ ثبت:</dt>
                    <dd class="col-sm-8">{{ \App\Helpers\PersianDateHelper::toPersian(\Carbon\Carbon::parse($patient->created_at)->format('Y-m-d')) }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection