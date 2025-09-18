<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیستم نوبت دهی مطب دکتر عزیزی</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4 mb-4">سیستم نوبت دهی مطب دکتر عزیزی</h1>
                <p class="lead mb-5">برای دریافت نوبت معاینه اولیه، فرم زیر را تکمیل کنید.</p>
                
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">نوبت جدید</h5>
                        <p class="card-text">برای دریافت نوبت معاینه اولیه، روی دکمه زیر کلیک کنید.</p>
                        <a href="{{ route('appointments.booking') }}" class="btn btn-primary btn-lg">دریافت نوبت</a>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('appointments.admin.dashboard') }}" class="btn btn-outline-secondary">ورود به پنل مدیریت</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>