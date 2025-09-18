@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">جزئیات نوبت</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>نام بیمار:</strong> {{ $appointment->patient_name }}</p>
                            <p><strong>شماره تماس:</strong> {{ $appointment->patient_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>تاریخ مراجعه:</strong> {{ $persianDate }}</p>
                            <p><strong>ساعت مراجعه:</strong> {{ $appointment->appointment_time }}</p>
                        </div>
                    </div>
                    
                    @if($appointment->notes)
                        <div class="mt-3">
                            <p><strong>توضیحات:</strong></p>
                            <p>{{ $appointment->notes }}</p>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('appointments.booking') }}" class="btn btn-primary">بازگشت به فرم نوبت</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border-radius: 12px;
}

.card-header {
    border-bottom: 1px solid #E2E8F0;
    border-radius: 12px 12px 0 0 !important;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.fw-medium {
    font-weight: 500;
    color: #374151;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-warning:hover,
.btn-outline-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

.alert {
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .btn-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn-group .btn {
        flex: 1;
    }
}
</style>
@endpush