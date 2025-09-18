@extends('layouts.app')

@section('title', 'ویرایش لابراتوار')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">ویرایش لابراتوار</h1>
        <p class="text-muted mb-0">ویرایش اطلاعات لابراتوار "{{ $laboratory->name }}"</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('laboratories.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            بازگشت به لیست لابراتوارها
        </a>
    </div>
</div>

<!-- Edit Laboratory Form -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">اطلاعات لابراتوار</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('laboratories.update', $laboratory) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">نام لابراتوار *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $laboratory->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">تلفن</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $laboratory->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="address" class="form-label">آدرس</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $laboratory->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $laboratory->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            لابراتوار فعال باشد
                        </label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('laboratories.index') }}" class="btn btn-secondary">انصراف</a>
                <button type="submit" class="btn btn-primary">به‌روزرسانی اطلاعات</button>
            </div>
        </form>
    </div>
</div>
@endsection