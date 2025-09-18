<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentTimeSlot;
use Verta;

class SimpleAppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('patient')->orderBy('appointment_date', 'asc')->get();
        return view('appointments.admin.index', compact('appointments'));
    }

    public function create()
    {
        // Generate next 30 days for booking
        $dates = [];
        for ($i = 0; $i < 30; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            $persianDate = Verta::instance($date)->format('Y/m/d');
            $dates[$date] = $persianDate;
        }
        
        // Get available time slots for today as an example
        $timeSlots = AppointmentTimeSlot::where('date', date('Y-m-d'))
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();
        
        return view('appointments.booking', compact('dates', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'notes' => 'nullable|string'
        ]);

        // Create appointment (simplified)
        $appointment = Appointment::create([
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
            'type' => 'consultation',
            'status' => 'scheduled'
        ]);

        return redirect()->route('appointments.booking')
            ->with('success', 'نوبت شما با موفقیت ثبت شد.');
    }

    public function show(Appointment $appointment)
    {
        $persianDate = Verta::instance($appointment->appointment_date)->format('Y/m/d');
        return view('appointments.show', compact('appointment', 'persianDate'));
    }

    // Admin methods
    public function adminDashboard()
    {
        return view('appointments.admin.dashboard');
    }

    public function timeSlots()
    {
        $timeSlots = AppointmentTimeSlot::orderBy('date')->orderBy('start_time')->get();
        return view('appointments.admin.time_slots', compact('timeSlots'));
    }

    public function storeTimeSlot(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        AppointmentTimeSlot::create([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => true
        ]);

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'بازه زمانی با موفقیت اضافه شد.');
    }

    public function destroyTimeSlot($id)
    {
        $timeSlot = AppointmentTimeSlot::findOrFail($id);
        $timeSlot->delete();

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'بازه زمانی با موفقیت حذف شد.');
    }
}