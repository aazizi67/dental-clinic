<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'سیستم مطب دندانپزشکی دکتر علی عزیزی')</title>
    
    <!-- فونت فارسی -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Jalali Date Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.css">

    <style>
        * {
            font-family: 'Vazirmatn', 'Tahoma', sans-serif;
        }
        
        /* اضافه کردن استایل برای mobile menu toggle */
        .mobile-menu-toggle {
            display: none;
        }
        
        /* استایل برای زمان و تاریخ */
        .datetime-display {
            background: rgba(59, 130, 246, 0.1);
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        
        .datetime-display i {
            color: var(--primary-color);
        }
        
        @media (max-width: 767px) {
            .mobile-menu-toggle {
                display: block;
            }
            .sidebar {
                position: fixed;
                top: 76px;
                left: -100%;
                width: 280px;
                height: calc(100vh - 76px);
                z-index: 1000;
                transition: left 0.3s ease;
                background: white;
            }
            .sidebar.show {
                left: 0;
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }
            .main-content {
                margin-right: 0 !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Mobile menu toggle -->
            <button class="btn btn-outline-secondary mobile-menu-toggle me-3" type="button" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-heart-pulse-fill me-2"></i>
                <span class="d-none d-md-inline">مطب دندانپزشکی دکتر علی عزیزی</span>
                <span class="d-md-none">مطب دکتر عزیزی</span>
            </a>
            
            <div class="d-flex align-items-center">
                <!-- نمایش زمان و تاریخ فعلی -->
                <div class="me-3 d-none d-lg-block">
                    <div class="datetime-display">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>
                            <span id="current-date"></span>
                            <span class="mx-2">|</span>
                            <i class="bi bi-clock me-1"></i>
                            <span id="current-time"></span>
                        </div>
                    </div>
                </div>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">تنظیمات</h6></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>تنظیمات حساب</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>خروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- Sidebar overlay for mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column nav-pills">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>
                                داشبورد
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" 
                               href="{{ route('patients.index') }}">
                                <i class="bi bi-people me-2"></i>
                                بیماران
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" 
                               href="{{ route('appointments.index') }}">
                                <i class="bi bi-calendar-check me-2"></i>
                                نوبت‌ها
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('treatments.*') ? 'active' : '' }}" 
                               href="{{ route('treatments.index') }}">
                                <i class="bi bi-clipboard2-check me-2"></i>
                                ثبت معاینه
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" 
                               href="{{ route('payments.index') }}">
                                <i class="bi bi-cash-coin me-2"></i>
                                پرداخت‌ها
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('accounting.*') ? 'active' : '' }}" 
                               data-bs-toggle="collapse" href="#accountingSubmenu" role="button" 
                               aria-expanded="{{ request()->routeIs('accounting.*') ? 'true' : 'false' }}">
                                <i class="bi bi-calculator me-2"></i>
                                حسابداری
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('accounting.*') ? 'show' : '' }}" id="accountingSubmenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('accounting.index') ? 'active' : '' }}" 
                                           href="{{ route('accounting.index') }}">
                                            <i class="bi bi-house me-2"></i>
                                            داشبورد
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('accounting.transactions*') ? 'active' : '' }}" 
                                           href="{{ route('accounting.transactions') }}">
                                            <i class="bi bi-list-ul me-2"></i>
                                            تراکنش‌ها
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('accounting.expenses*') ? 'active' : '' }}" 
                                           href="{{ route('accounting.expenses') }}">
                                            <i class="bi bi-receipt me-2"></i>
                                            هزینه‌ها
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('accounting.chart-of-accounts') ? 'active' : '' }}" 
                                           href="{{ route('accounting.chart-of-accounts') }}">
                                            <i class="bi bi-diagram-3 me-2"></i>
                                            طرف حساب‌ها
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('accounting.reports') ? 'active' : '' }}" 
                                           href="{{ route('accounting.reports') }}">
                                            <i class="bi bi-bar-chart me-2"></i>
                                            گزارشات
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('accounting.analytics') ? 'active' : '' }}" 
                                           href="{{ route('accounting.analytics') }}">
                                            <i class="bi bi-graph-up me-2"></i>
                                            تحلیل مالی
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('accounting.backup') ? 'active' : '' }}" 
                                           href="{{ route('accounting.backup') }}">
                                            <i class="bi bi-download me-2"></i>
                                            پشتیبان و خروجی
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <!-- خط جداکننده -->
                        <hr class="my-3">
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}" 
                               href="{{ route('calendar') }}">
                                <i class="bi bi-calendar3 me-2"></i>
                                تقویم
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports') ? 'active' : '' }}" 
                               href="{{ route('reports') }}">
                                <i class="bi bi-graph-up me-2"></i>
                                گزارشات
                            </a>
                        </li>
                        
                        <!-- Attendance Management -->
                        @if(auth()->user()->hasRole(['doctor', 'secretary', 'assistant']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" 
                               href="{{ route('attendance.index') }}">
                                <i class="bi bi-person-check me-2"></i>
                                حضور و غیاب
                            </a>
                        </li>
                        @endif
                        
                        <!-- Laboratory Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('laboratories.*') ? 'active' : '' }}" 
                               data-bs-toggle="collapse" href="#laboratorySubmenu" role="button" 
                               aria-expanded="{{ request()->routeIs('laboratories.*') ? 'true' : 'false' }}">
                                <i class="bi bi-building me-2"></i>
                                لابراتوارها
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('laboratories.*') ? 'show' : '' }}" id="laboratorySubmenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('laboratories.index') || request()->routeIs('laboratories.create') || request()->routeIs('laboratories.edit') ? 'active' : '' }}" 
                                           href="{{ route('laboratories.index') }}">
                                            <i class="bi bi-list-ul me-2"></i>
                                            لیست لابراتوارها
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('laboratories.transactions*') ? 'active' : '' }}" 
                                           href="{{ route('laboratories.transactions') }}">
                                            <i class="bi bi-clipboard-data me-2"></i>
                                            تراکنش‌ها
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('laboratories.reports') ? 'active' : '' }}" 
                                           href="{{ route('laboratories.reports') }}">
                                            <i class="bi bi-file-text me-2"></i>
                                            گزارش‌ها
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Jalali Datepicker Library -->
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.js"></script>
    <!-- Persian Date Library -->
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Function to convert English digits to Persian digits (same logic as in app.js)
        function toPersianDigits(input) {
            if (input == null) return '';
            const persianDigitMap = {
                '0': '۰', '1': '۱', '2': '۲', '3': '۳', '4': '۴',
                '5': '۵', '6': '۶', '7': '۷', '8': '۸', '9': '۹'
            };
            return String(input).replace(/[0-9]/g, d => persianDigitMap[d]);
        }
        
        // Function to convert Persian digits back to English digits
        function toEnglishDigits(input) {
            if (input == null) return '';
            const englishDigitMap = {
                '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
                '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9'
            };
            return String(input).replace(/[۰-۹]/g, d => englishDigitMap[d]);
        }
        
        // Initialize JalaliDatePicker
        $(document).ready(function() {
            // Check if JalaliDatePicker is available
            if (typeof jalaliDatepicker !== 'undefined') {
                console.log('JalaliDatePicker loaded successfully');
                // Initialize all elements with data-jdp attribute with Persian digit support
                jalaliDatepicker.startWatch({
                    persianDigits: true,
                    separatorChars: {
                        date: '/'
                    }
                });
                
                // Add event listener for when date is selected
                $(document).on('change', '[data-jdp]', function() {
                    const inputValue = $(this).val();
                    if (inputValue) {
                        // Store the English digits version in a data attribute
                        $(this).data('english-value', inputValue);
                        // Display the Persian digits version to the user
                        const persianValue = toPersianDigits(inputValue);
                        $(this).val(persianValue);
                    }
                });
                
                // Before the date picker opens, restore the English digits value
                $(document).on('focus', '[data-jdp]', function() {
                    const englishValue = $(this).data('english-value');
                    if (englishValue) {
                        $(this).val(englishValue);
                    }
                });
            } else {
                console.log('JalaliDatePicker not available, using persian-datepicker');
            }
        });
        
        // Mobile menu toggle
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            const overlay = $('#sidebarOverlay');
            const toggle = $('#sidebarToggle');
            
            toggle.on('click', function() {
                sidebar.toggleClass('show');
                overlay.toggleClass('show');
            });
            
            overlay.on('click', function() {
                sidebar.removeClass('show');
                overlay.removeClass('show');
            });
            
            // بستن menu هنگام کلیک روی لینک‌ها در mobile
            if (window.innerWidth <= 767) {
                $('.nav-link').on('click', function() {
                    sidebar.removeClass('show');
                    overlay.removeClass('show');
                });
            }
        });
        
        // نمایش زمان و تاریخ فعلی
        function updateDateTime() {
            const now = new Date();
            
            // نمایش زمان
            const timeString = now.toLocaleTimeString('fa-IR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#current-time').text(timeString);
            
            // تبدیل به تاریخ شمسی با فرمت مورد نظر
            const weekday = new Date().toLocaleDateString('fa-IR', { weekday: 'long' });
            const day = new Date().toLocaleDateString('fa-IR', { day: 'numeric' });
            const month = new Date().toLocaleDateString('fa-IR', { month: 'long' });
            const year = new Date().toLocaleDateString('fa-IR', { year: 'numeric' });
            
            // تنظیم فرمت: شنبه، ۲۲ شهریور ۱۴۰۴
            const formattedDate = `${weekday}، ${day} ${month} ${year}`;
            $('#current-date').text(formattedDate);
        }
        
        // به‌روزرسانی هر ثانیه
        setInterval(updateDateTime, 1000);
        updateDateTime(); // اجرای اولیه
        
        // اضافه کردن افکت محو شدن به alert ها
        $('.alert').each(function() {
            const alert = $(this);
            setTimeout(function() {
                alert.fadeOut(500);
            }, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>