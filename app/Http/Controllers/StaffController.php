<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::orderBy('first_name')->paginate(20);
        return view('staff.index', compact('staff'));
    }
    
    public function create()
    {
        return view('staff.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|size:10|unique:staff',
            'phone' => 'required|string|max:15',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'role' => 'required|in:doctor,secretary,assistant,nurse,cleaner,other',
            'hourly_rate' => 'required|numeric|min:0',
            'hire_date' => 'nullable|date',
            'birth_date' => 'nullable|date',
        ]);
        
        Staff::create($request->all());
        
        return redirect()->route('staff.index')->with('success', 'پرسنل جدید با موفقیت اضافه شد.');
    }
    
    public function show(Staff $staff)
    {
        // Load attendance records for the staff member
        $attendanceRecords = $staff->attendanceRecords()->orderBy('date', 'desc')->paginate(30);
        return view('staff.show', compact('staff', 'attendanceRecords'));
    }
    
    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }
    
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|size:10|unique:staff,national_id,' . $staff->id,
            'phone' => 'required|string|max:15',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'role' => 'required|in:doctor,secretary,assistant,nurse,cleaner,other',
            'hourly_rate' => 'required|numeric|min:0',
            'hire_date' => 'nullable|date',
            'birth_date' => 'nullable|date',
            'is_active' => 'boolean'
        ]);
        
        $staff->update($request->all());
        
        return redirect()->route('staff.index')->with('success', 'اطلاعات پرسنل با موفقیت به‌روزرسانی شد.');
    }
    
    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'پرسنل با موفقیت حذف شد.');
    }
}