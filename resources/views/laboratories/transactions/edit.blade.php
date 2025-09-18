@extends('layouts.app')

@section('title', 'ویرایش تراکنش لابراتوار')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">ویرایش تراکنش لابراتوار</h1>
        <p class="text-muted mb-0">ویرایش اطلاعات تراکنش</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('laboratories.transactions') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            بازگشت به لیست تراکنش‌ها
        </a>
    </div>
</div>

<!-- Edit Transaction Form -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">اطلاعات تراکنش</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('laboratories.transactions.update', $transaction) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="laboratory_id" class="form-label">لابراتوار *</label>
                        <select class="form-select @error('laboratory_id') is-invalid @enderror" id="laboratory_id" name="laboratory_id" required>
                            <option value="">انتخاب لابراتوار</option>
                            @foreach($laboratories as $laboratory)
                                <option value="{{ $laboratory->id }}" {{ old('laboratory_id', $transaction->laboratory_id) == $laboratory->id ? 'selected' : '' }}>
                                    {{ $laboratory->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('laboratory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">بیمار *</label>
                        <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                            <option value="">انتخاب بیمار</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $transaction->patient_id) == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="doctor_id" class="form-label">دندانپزشک</label>
                        <select class="form-select @error('doctor_id') is-invalid @enderror" id="doctor_id" name="doctor_id">
                            <option value="">انتخاب دندانپزشک</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $transaction->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="date" class="form-label">تاریخ *</label>
                        <input type="text" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', verta($transaction->date)->format('Y/m/d')) }}" placeholder="مثلاً ۱۴۰۳/۰۹/۲۲" autocomplete="off" readonly required>
                        <input type="hidden" id="date_gregorian" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}">
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="time" class="form-label">ساعت *</label>
                        <input type="text" class="form-control @error('time') is-invalid @enderror" id="time" name="time" value="{{ old('time', $transaction->time) }}" placeholder="مثلاً ۱۴:۳۰">
                        @error('time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label">نوع تراکنش *</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">انتخاب نوع</option>
                            <option value="entry" {{ old('type', $transaction->type) == 'entry' ? 'selected' : '' }}>خروجی (ارسال به لابراتوار)</option>
                            <option value="exit" {{ old('type', $transaction->type) == 'exit' ? 'selected' : '' }}>ورودی (دریافت از لابراتوار)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category" class="form-label">دسته‌بندی *</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">انتخاب دسته‌بندی</option>
                            <option value="post" {{ old('category', $transaction->category) == 'post' ? 'selected' : '' }}>پست</option>
                            <option value="crown" {{ old('category', $transaction->category) == 'crown' ? 'selected' : '' }}>روکش</option>
                            <option value="laminat" {{ old('category', $transaction->category) == 'laminat' ? 'selected' : '' }}>لمینت</option>
                            <option value="implant_crown" {{ old('category', $transaction->category) == 'implant_crown' ? 'selected' : '' }}>روکش ایمپلنت</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">قیمت (تومان)</label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $transaction->price) }}" min="0" step="1000">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">توضیحات</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $transaction->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('laboratories.transactions') }}" class="btn btn-secondary">انصراف</a>
                <button type="submit" class="btn btn-primary">به‌روزرسانی تراکنش</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker
    $('#date').MdPersianDateTimePicker({
        targetTextSelector: '#date',
        targetDateSelector: '#date_gregorian',
        dateFormat: 'yyyy/MM/dd',
        isGregorian: false,
        modalMode: false,
        englishNumber: false,
        enableTimePicker: false
    });
});
</script>
@endpush