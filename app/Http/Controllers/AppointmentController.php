<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Services\LocalCalendarService;
use App\Services\ExternalCalendarService;
use Illuminate\Support\Facades\Config;

class AppointmentController extends Controller
{
    protected $calendarService;
    protected $externalCalendarService;
    protected $useExternalService;
    
    public function __construct(LocalCalendarService $localCalendarService, ExternalCalendarService $externalCalendarService)
    {
        $this->calendarService = $localCalendarService;
        $this->externalCalendarService = $externalCalendarService;
        // Set based on configuration
        $this->useExternalService = Config::get('calendar.service', 'local') === 'external';
    }
    
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);
        
        if ($request->has('date') && $request->date) {
            $query->whereDate('appointment_date', $request->date);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $appointments = $query->orderBy('appointment_date')
                             ->orderBy('appointment_time')
                             ->paginate(20);
        
        return view('appointments.index', compact('appointments'));
    }
    
    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        
        return view('appointments.create', compact('patients', 'doctors'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required',
            'type' => 'required|in:consultation,treatment,follow_up,emergency',
            'chief_complaint' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // محاسبه مدت زمان بر اساس زمان شروع و پایان
        $startTime = \Carbon\Carbon::parse($request->start_time);
        $endTime = \Carbon\Carbon::parse($request->end_time);
        $duration = $endTime->diffInMinutes($startTime);

        // بررسی اینکه زمان پایان بعد از زمان شروع باشد
        if ($duration <= 0) {
            return back()->withErrors([
                'end_time' => 'زمان پایان باید بعد از زمان شروع باشد.'
            ])->withInput();
        }

        // بررسی حداقل مدت زمان ۱۵ دقیقه
        if ($duration < 15) {
            return back()->withErrors([
                'end_time' => 'حداقل مدت زمان نوبت ۱۵ دقیقه است.'
            ])->withInput();
        }
        
        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->start_time, // نگه‌داری از فیلد قدیمی برای سازگاری
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration' => $duration,
            'type' => $request->type,
            'status' => 'scheduled',
            'chief_complaint' => $request->chief_complaint,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);
        
        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'نوبت با موفقیت ثبت شد.');
    }
    
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'createdBy']);
        
        return view('appointments.show', compact('appointment'));
    }
    
    public function checkConflict(Request $request)
    {
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        if (!$date || !$startTime || !$endTime) {
            return response()->json(['conflict' => false]);
        }
        
        // تبدیل زمان‌ها به فرمت Carbon
        $newStart = \Carbon\Carbon::parse($date . ' ' . $startTime);
        $newEnd = \Carbon\Carbon::parse($date . ' ' . $endTime);
        
        // بررسی تداخل با نوبت‌های موجود که از فیلدهای جدید استفاده می‌کنند
        $conflictWithNewFormat = Appointment::whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->where(function($query) use ($newStart, $newEnd) {
                $query->where(function($q) use ($newStart, $newEnd) {
                    // نوبت موجود که با نوبت جدید تداخل دارد
                    $q->whereRaw('DATE(appointment_date) = ? AND (
                        (TIME(start_time) < ? AND TIME(end_time) > ?) OR
                        (TIME(start_time) < ? AND TIME(end_time) > ?) OR
                        (TIME(start_time) >= ? AND TIME(end_time) <= ?)
                    )', [
                        $newStart->format('Y-m-d'),
                        $newEnd->format('H:i:s'), $newStart->format('H:i:s'),
                        $newStart->format('H:i:s'), $newStart->format('H:i:s'),
                        $newStart->format('H:i:s'), $newEnd->format('H:i:s')
                    ]);
                });
            })
            ->exists();
            
        // بررسی تداخل با نوبت‌های قدیمی که فقط appointment_time و duration دارند
        $conflictWithOldFormat = Appointment::whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) {
                $query->whereNull('start_time')->orWhereNull('end_time');
            })
            ->where(function($query) use ($newStart, $newEnd) {
                $query->whereRaw('(
                    TIME(appointment_time) < ? AND ADDTIME(TIME(appointment_time), SEC_TO_TIME(duration * 60)) > ?
                ) OR (
                    TIME(appointment_time) < ? AND TIME(appointment_time) >= ?
                ) OR (
                    TIME(appointment_time) >= ? AND ADDTIME(TIME(appointment_time), SEC_TO_TIME(duration * 60)) <= ?
                )', [
                    $newEnd->format('H:i:s'), $newStart->format('H:i:s'),
                    $newStart->format('H:i:s'), $newStart->format('H:i:s'),
                    $newStart->format('H:i:s'), $newEnd->format('H:i:s')
                ]);
            })
            ->exists();
        
        $conflict = $conflictWithNewFormat || $conflictWithOldFormat;
        
        return response()->json(['conflict' => $conflict]);
    }
    
    /**
     * Display the Persian calendar with events and holidays
     */
    public function calendar()
    {
        // Get current year and month for initial data
        $currentYear = verta()->year;
        $currentMonth = verta()->month;
        
        return view('appointments.calendar');
    }
    
    /**
     * Get calendar events for a specific month
     */
    public function getCalendarEvents($year, $month)
    {
        try {
            // Use external service if enabled, otherwise use local service
            $service = $this->useExternalService ? $this->externalCalendarService : $this->calendarService;
            
            // Get events from the calendar service
            $events = $service->getCalendarEvents($year, $month);
            
            // Get appointments for this month from the database
            $startDate = verta()->createJalaliDate($year, $month, 1)->toCarbon();
            $endDate = verta()->createJalaliDate($year, $month, 1)->addMonth()->subDay()->toCarbon();
            
            $appointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
                ->with('patient')
                ->get();
            
            // Format appointments as events
            $appointmentEvents = [];
            foreach ($appointments as $appointment) {
                $jalaliDate = verta($appointment->appointment_date);
                $dateKey = $jalaliDate->format('Y-n-j');
                
                if (!isset($appointmentEvents[$dateKey])) {
                    $appointmentEvents[$dateKey] = [];
                }
                
                $appointmentEvents[$dateKey][] = [
                    'title' => 'نوبت: ' . $appointment->patient->full_name,
                    'time' => $appointment->appointment_time ? verta($appointment->appointment_time)->format('H:i') : null,
                    'type' => 'نوبت',
                    'description' => $appointment->chief_complaint
                ];
            }
            
            // Merge events
            foreach ($appointmentEvents as $date => $appointments) {
                if (!isset($events[$date])) {
                    $events[$date] = [];
                }
                $events[$date] = array_merge($events[$date], $appointments);
            }
            
            // Get holidays
            $holidays = [];
            $daysInMonth = verta()->createJalaliDate($year, $month, 1)->daysInMonth;
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayData = $service->getDayEvents($year, $month, $day);
                $dateKey = "{$year}-{$month}-{$day}";
                
                foreach ($dayData['events'] as $event) {
                    if (!empty($event['is_holiday'])) {
                        $holidays[$dateKey] = true;
                        break;
                    }
                }
            }
            
            return response()->json([
                'events' => $events,
                'holidays' => $holidays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'events' => [],
                'holidays' => []
            ]);
        }
    }
    
    /**
     * Get events for a specific day
     */
    public function getDayEvents($year, $month, $day)
    {
        try {
            // Get events from the calendar service
            $eventsData = $this->calendarService->getDayEvents($year, $month, $day);
            
            // Get appointments for this day from the database
            $date = verta()->createJalaliDate($year, $month, $day)->toCarbon();
            
            $appointments = Appointment::whereDate('appointment_date', $date)
                ->with('patient')
                ->get();
            
            // Format appointments as events
            $appointmentEvents = [];
            foreach ($appointments as $appointment) {
                $appointmentEvents[] = [
                    'title' => 'نوبت: ' . $appointment->patient->full_name,
                    'time' => $appointment->appointment_time ? verta($appointment->appointment_time)->format('H:i') : null,
                    'type' => 'نوبت',
                    'description' => $appointment->chief_complaint
                ];
            }
            
            // Combine events and holidays
            $allEvents = array_merge($eventsData['events'], $appointmentEvents);
            
            return response()->json([
                'events' => $allEvents,
                'holidays' => $eventsData['holidays']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'events' => [],
                'holidays' => []
            ]);
        }
    }
    
    /**
     * Get holidays for a specific year
     */
    public function getHolidays($year)
    {
        try {
            $holidays = $this->calendarService->getHolidays($year);
            return response()->json($holidays);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}