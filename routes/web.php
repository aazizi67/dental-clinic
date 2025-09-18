<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SimpleAppointmentController;
use App\Http\Controllers\Api\PatientController as ApiPatientController;
use App\Helpers\GenderDetector;

// Test route
Route::get('/test', function () {
    return 'Laravel is working!';
});

// Test gender detection
Route::get('/test-gender', function () {
    $testNames = ['علی', 'فاطمه', 'محمد', 'زهرا', 'احمد'];
    $results = [];
    
    foreach ($testNames as $name) {
        $results[$name] = GenderDetector::detectGender($name);
    }
    
    return response()->json($results);
});

// Appointment system routes (publicly accessible)
Route::prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/', [SimpleAppointmentController::class, 'create'])->name('home');
    Route::get('/booking', [SimpleAppointmentController::class, 'create'])->name('booking');
    Route::post('/booking', [SimpleAppointmentController::class, 'store'])->name('store');
    
    // Admin routes for appointment system (protected)
    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
        Route::get('/dashboard', [SimpleAppointmentController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/appointments', [SimpleAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{id}', [SimpleAppointmentController::class, 'show'])->name('appointments.show');
        
        // Time slots management
        Route::get('/time-slots', [SimpleAppointmentController::class, 'timeSlots'])->name('time-slots.index');
        Route::post('/time-slots', [SimpleAppointmentController::class, 'storeTimeSlot'])->name('time-slots.store');
        Route::delete('/time-slots/{id}', [SimpleAppointmentController::class, 'destroyTimeSlot'])->name('time-slots.destroy');
    });
});

// صفحه اصلی - ریدایرکت به پنل
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (نیاز به ورود)
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Patient Management
    Route::resource('patients', PatientController::class);
    Route::post('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    
    // Appointments (original system)
    Route::resource('appointments', AppointmentController::class);
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
    
    // Treatment Plans
    Route::resource('treatments', TreatmentController::class);
    Route::get('/chart', [TreatmentController::class, 'chart'])->name('treatments.chart');
    
    // Payments
    Route::resource('payments', PaymentController::class);
    Route::get('/reports', [PaymentController::class, 'reports'])->name('reports');
    
    // Accounting System
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/', [AccountingController::class, 'index'])->name('index');
        Route::get('/transactions', [AccountingController::class, 'transactions'])->name('transactions');
        Route::get('/transactions/create', [AccountingController::class, 'createTransaction'])->name('transactions.create');
        Route::post('/transactions', [AccountingController::class, 'storeTransaction'])->name('transactions.store');
        Route::get('/chart-of-accounts', [AccountingController::class, 'chartOfAccounts'])->name('chart-of-accounts');
        Route::get('/reports', [AccountingController::class, 'reports'])->name('reports');
        
        // Expense Management
        Route::get('/expenses', [AccountingController::class, 'expenses'])->name('expenses');
        Route::post('/expenses', [AccountingController::class, 'storeExpense'])->name('expenses.store');
        Route::get('/expenses/{expense}', [AccountingController::class, 'showExpense'])->name('expenses.show');
        Route::put('/expenses/{expense}', [AccountingController::class, 'updateExpense'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [AccountingController::class, 'destroyExpense'])->name('expenses.destroy');
        
        // Financial Analytics
        Route::get('/analytics', [AccountingController::class, 'analytics'])->name('analytics');
        
        // Backup and Export
        Route::get('/backup', [AccountingController::class, 'backup'])->name('backup');
        Route::post('/backup', [AccountingController::class, 'createBackup'])->name('backup.create');
        Route::post('/export', [AccountingController::class, 'exportReport'])->name('export');
    });
    
    // Management Pages (فقط دکتر)
    Route::middleware('can:manage-system')->group(function () {
        Route::get('/treatment-types', [TreatmentController::class, 'types'])->name('treatment-types.index');
        Route::post('/treatment-types', [TreatmentController::class, 'storeType'])->name('treatment-types.store');
        Route::get('/prices', [TreatmentController::class, 'prices'])->name('prices.index');
        Route::post('/prices', [TreatmentController::class, 'updatePrices'])->name('prices.update');
    });
    
    // Staff Management
    Route::resource('staff', StaffController::class);
    
    // Attendance Management
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::get('/working-hours-report', [AttendanceController::class, 'workingHoursReport'])->name('working-hours-report');
    });
    
    // Laboratory Management
    Route::prefix('laboratories')->name('laboratories.')->group(function () {
        // Laboratories
        Route::get('/', [LaboratoryController::class, 'index'])->name('index');
        Route::get('/create', [LaboratoryController::class, 'create'])->name('create');
        Route::post('/', [LaboratoryController::class, 'store'])->name('store');
        Route::get('/{laboratory}/edit', [LaboratoryController::class, 'edit'])->name('edit');
        Route::put('/{laboratory}', [LaboratoryController::class, 'update'])->name('update');
        Route::delete('/{laboratory}', [LaboratoryController::class, 'destroy'])->name('destroy');
        
        // Transactions
        Route::get('/transactions', [LaboratoryController::class, 'transactions'])->name('transactions');
        Route::get('/transactions/create', [LaboratoryController::class, 'createTransaction'])->name('transactions.create');
        Route::post('/transactions', [LaboratoryController::class, 'storeTransaction'])->name('transactions.store');
        Route::get('/transactions/{transaction}/edit', [LaboratoryController::class, 'editTransaction'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [LaboratoryController::class, 'updateTransaction'])->name('transactions.update');
        Route::delete('/transactions/{transaction}', [LaboratoryController::class, 'destroyTransaction'])->name('transactions.destroy');
        
        // Reports
        Route::get('/reports', [LaboratoryController::class, 'reports'])->name('reports');
    });
    
    // Test Routes
    Route::get('/test-external-calendar', [TestController::class, 'testExternalCalendar']);
    Route::get('/test-calendar-switch', [TestController::class, 'testCalendarSwitch']);
    
    // Calendar Routes
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    
    // Calendar API Routes
    Route::prefix('calendar')->group(function () {
        Route::get('/events/{year}/{month}', [CalendarController::class, 'getMonthEvents']);
        Route::get('/day/{year}/{month}/{day}', [CalendarController::class, 'getDayEvents']);
        Route::post('/note', [CalendarController::class, 'saveNote']);
        Route::post('/note/delete', [CalendarController::class, 'deleteNote']);
    });
});

// API Routes for AJAX
Route::prefix('api')->middleware('auth')->group(function () {
    Route::post('/get-patient', [PatientController::class, 'getPatient']);
    Route::post('/get-prices', [TreatmentController::class, 'getPrices']);
    Route::get('/search-patients', [PatientController::class, 'searchPatients']);
    Route::post('/quick-create-patient', [PatientController::class, 'quickCreatePatient']);
    Route::post('/get-patient-treatments', [TreatmentController::class, 'getPatientTreatments']);
    Route::post('/check-appointment-conflict', [AppointmentController::class, 'checkConflict']);
    
    // Test external calendar API
    Route::get('/test-external-calendar-api', function () {
        $service = app(\App\Services\ExternalCalendarService::class);
        $events = $service->getDayEvents(1404, 6, 24); // Shahrivar 24, 1404
        return response()->json($events);
    });
});

// Public API route for gender detection (no auth required and no CSRF protection)
Route::post('/api/detect-gender', [ApiPatientController::class, 'detectGender']);

// New route for Gemini-based gender detection
Route::post('/api/gemini-detect-gender', [App\Http\Controllers\Api\GeminiGenderController::class, 'detectGender']);
