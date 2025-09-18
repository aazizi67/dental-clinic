<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\Staff;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        // Get all active staff members
        $staff = Staff::active()->get();
        
        // Get today's date
        $today = Carbon::today();
        
        // Get attendance records for today
        $attendanceRecords = AttendanceRecord::where('date', $today)
                                            ->with('staff')
                                            ->get()
                                            ->keyBy('staff_id');
        
        return view('attendance.index', compact('staff', 'attendanceRecords', 'today'));
    }
    
    public function checkIn(Request $request)
    {
        $staffId = $request->get('staff_id');
        
        // If no staff_id provided, use the logged in user's associated staff record
        if (!$staffId) {
            // For now, we'll just return an error
            // In a real implementation, you might link users to staff members
            return response()->json([
                'success' => false,
                'message' => 'لطفاً پرسنل را انتخاب کنید.'
            ]);
        }
        
        $staff = Staff::findOrFail($staffId);
        $today = Carbon::today();
        
        // Check if staff already has an attendance record for today
        $attendanceRecord = AttendanceRecord::firstOrCreate(
            ['staff_id' => $staff->id, 'date' => $today],
            ['check_in_method' => $request->method ?? 'manual']
        );
        
        // If already checked in, return error
        if ($attendanceRecord->isCheckedIn()) {
            return response()->json([
                'success' => false,
                'message' => 'این پرسنل قبلاً ورود خود را ثبت کرده‌است.'
            ]);
        }
        
        // Update check-in time
        $attendanceRecord->check_in_time = Carbon::now()->format('H:i:s');
        $attendanceRecord->check_in_method = $request->method ?? 'manual';
        $attendanceRecord->save();
        
        return response()->json([
            'success' => true,
            'message' => 'ورود پرسنل با موفقیت ثبت شد.',
            'check_in_time' => $attendanceRecord->check_in_time
        ]);
    }
    
    public function checkOut(Request $request)
    {
        $staffId = $request->get('staff_id');
        
        // If no staff_id provided, use the logged in user's associated staff record
        if (!$staffId) {
            // For now, we'll just return an error
            // In a real implementation, you might link users to staff members
            return response()->json([
                'success' => false,
                'message' => 'لطفاً پرسنل را انتخاب کنید.'
            ]);
        }
        
        $staff = Staff::findOrFail($staffId);
        $today = Carbon::today();
        
        // Find attendance record for today
        $attendanceRecord = AttendanceRecord::where('staff_id', $staff->id)
                                          ->where('date', $today)
                                          ->first();
        
        // If no record found, return error
        if (!$attendanceRecord) {
            return response()->json([
                'success' => false,
                'message' => 'ابتدا باید ورود پرسنل را ثبت کنید.'
            ]);
        }
        
        // If already checked out, return error
        if ($attendanceRecord->isCheckedOut()) {
            return response()->json([
                'success' => false,
                'message' => 'این پرسنل قبلاً خروج خود را ثبت کرده‌است.'
            ]);
        }
        
        // Update check-out time
        $attendanceRecord->check_out_time = Carbon::now()->format('H:i:s');
        $attendanceRecord->check_out_method = $request->method ?? 'manual';
        $attendanceRecord->save();
        
        // Calculate working hours
        $workingHours = $attendanceRecord->getWorkingHours();
        
        return response()->json([
            'success' => true,
            'message' => 'خروج پرسنل با موفقیت ثبت شد.',
            'check_out_time' => $attendanceRecord->check_out_time,
            'working_hours' => round($workingHours, 2)
        ]);
    }
    
    public function report(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        $staffId = $request->get('staff_id');
        
        $query = AttendanceRecord::with('staff')
                                ->whereBetween('date', [$startDate, $endDate]);
        
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }
        
        $records = $query->orderBy('date', 'desc')
                        ->paginate(30);
        
        $staff = Staff::active()->get();
        
        return view('attendance.report', compact('records', 'staff', 'startDate', 'endDate'));
    }
    
    // Detailed working hours report for salary calculation
    public function workingHoursReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        $staffId = $request->get('staff_id');
        
        $query = AttendanceRecord::with('staff')
                                ->whereBetween('date', [$startDate, $endDate])
                                ->whereNotNull('check_in_time')
                                ->whereNotNull('check_out_time');
        
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }
        
        $records = $query->orderBy('date', 'desc')
                        ->get();
        
        // Calculate totals
        $totalHours = 0;
        $totalSalary = 0;
        $staffHours = [];
        
        foreach ($records as $record) {
            $hours = $record->getWorkingHours();
            $salary = $record->getDailySalary();
            
            $totalHours += $hours;
            $totalSalary += $salary;
            
            // Group by staff member
            $staffKey = $record->staff->id;
            if (!isset($staffHours[$staffKey])) {
                $staffHours[$staffKey] = [
                    'staff' => $record->staff,
                    'total_hours' => 0,
                    'total_salary' => 0,
                    'records' => []
                ];
            }
            
            $staffHours[$staffKey]['total_hours'] += $hours;
            $staffHours[$staffKey]['total_salary'] += $salary;
            $staffHours[$staffKey]['records'][] = $record;
        }
        
        $staff = Staff::active()->get();
        
        return view('attendance.working-hours-report', compact('staffHours', 'staff', 'startDate', 'endDate', 'totalHours', 'totalSalary'));
    }
}