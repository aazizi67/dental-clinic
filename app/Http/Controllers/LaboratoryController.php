<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratory;
use App\Models\LaboratoryTransaction;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    public function index()
    {
        $laboratories = Laboratory::orderBy('name')->paginate(20);
        return view('laboratories.index', compact('laboratories'));
    }
    
    public function create()
    {
        return view('laboratories.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
        ]);
        
        Laboratory::create($request->all());
        
        return redirect()->route('laboratories.index')->with('success', 'لابراتوار جدید با موفقیت اضافه شد.');
    }
    
    public function edit(Laboratory $laboratory)
    {
        return view('laboratories.edit', compact('laboratory'));
    }
    
    public function update(Request $request, Laboratory $laboratory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $laboratory->update($request->all());
        
        return redirect()->route('laboratories.index')->with('success', 'اطلاعات لابراتوار با موفقیت به‌روزرسانی شد.');
    }
    
    public function destroy(Laboratory $laboratory)
    {
        $laboratory->delete();
        return redirect()->route('laboratories.index')->with('success', 'لابراتوار با موفقیت حذف شد.');
    }
    
    // Transactions section
    public function transactions()
    {
        $transactions = LaboratoryTransaction::with(['laboratory', 'patient', 'doctor'])
                                          ->orderBy('date', 'desc')
                                          ->orderBy('time', 'desc')
                                          ->paginate(30);
        
        $laboratories = Laboratory::active()->get();
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();
        
        return view('laboratories.transactions.index', compact('transactions', 'laboratories', 'patients', 'doctors'));
    }
    
    public function createTransaction()
    {
        $laboratories = Laboratory::active()->get();
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();
        
        return view('laboratories.transactions.create', compact('laboratories', 'patients', 'doctors'));
    }
    
    public function storeTransaction(Request $request)
    {
        $request->validate([
            'laboratory_id' => 'required|exists:laboratories,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'type' => 'required|in:entry,exit',
            'category' => 'required|in:post,crown,laminat,implant_crown',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);
        
        LaboratoryTransaction::create($request->all());
        
        return redirect()->route('laboratories.transactions')->with('success', 'تراکنش لابراتوار با موفقیت ثبت شد.');
    }
    
    public function editTransaction(LaboratoryTransaction $transaction)
    {
        $laboratories = Laboratory::active()->get();
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();
        
        return view('laboratories.transactions.edit', compact('transaction', 'laboratories', 'patients', 'doctors'));
    }
    
    public function updateTransaction(Request $request, LaboratoryTransaction $transaction)
    {
        $request->validate([
            'laboratory_id' => 'required|exists:laboratories,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'type' => 'required|in:entry,exit',
            'category' => 'required|in:post,crown,laminat,implant_crown',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);
        
        $transaction->update($request->all());
        
        return redirect()->route('laboratories.transactions')->with('success', 'تراکنش لابراتوار با موفقیت به‌روزرسانی شد.');
    }
    
    public function destroyTransaction(LaboratoryTransaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('laboratories.transactions')->with('success', 'تراکنش لابراتوار با موفقیت حذف شد.');
    }
    
    // Reports section
    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        $laboratoryId = $request->get('laboratory_id');
        $categoryId = $request->get('category');
        
        $query = LaboratoryTransaction::with(['laboratory', 'patient', 'doctor'])
                                    ->whereBetween('date', [$startDate, $endDate]);
        
        if ($laboratoryId) {
            $query->where('laboratory_id', $laboratoryId);
        }
        
        if ($categoryId) {
            $query->where('category', $categoryId);
        }
        
        $transactions = $query->orderBy('date', 'desc')
                            ->orderBy('time', 'desc')
                            ->paginate(30);
        
        $laboratories = Laboratory::active()->get();
        $categories = [
            'post' => 'پست',
            'crown' => 'روکش',
            'laminat' => 'لمینت',
            'implant_crown' => 'روکش ایمپلنت'
        ];
        
        return view('laboratories.reports', compact('transactions', 'laboratories', 'categories', 'startDate', 'endDate'));
    }
}