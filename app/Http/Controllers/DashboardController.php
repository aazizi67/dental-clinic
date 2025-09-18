<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\TreatmentPlan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // آمار کلی سیستم با بررسی null
        $stats = [
            'total_patients' => (int)Patient::count(),
            'today_appointments' => (int)Appointment::whereDate('appointment_date', today())->count(),
            'pending_appointments' => (int)Appointment::where('status', 'scheduled')->count(),
            'monthly_income' => (float)Payment::whereMonth('created_at', now()->month)->sum('amount'),
            'active_treatments' => (int)TreatmentPlan::where('status', 'active')->count(),
            'new_patients_today' => (int)Patient::whereDate('created_at', today())->count(),
            'completed_today' => (int)Appointment::whereDate('appointment_date', today())
                ->where('status', 'completed')->count(),
            'weekly_income' => (float)Payment::whereBetween('created_at', [
                now()->startOfWeek(), now()->endOfWeek()
            ])->sum('amount'),
        ];
        
        // نوبت‌های امروز با جزئیات بیشتر
        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();
        
        // نوبت‌های آینده (3 روز آینده)
        $upcomingAppointments = Appointment::with(['patient'])
            ->whereBetween('appointment_date', [today()->addDay(), today()->addDays(3)])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(8)
            ->get();
        
        // آمار درمان‌های محبوب
        $popularTreatments = TreatmentPlan::selectRaw('COUNT(*) as count, status')
            ->groupBy('status')
            ->get();
        
        return view('dashboard', compact(
            'stats', 
            'todayAppointments', 
            'upcomingAppointments',
            'popularTreatments'
        ));
    }
}