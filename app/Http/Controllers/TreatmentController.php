<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\TreatmentPlan;
use App\Models\TreatmentItem;
use App\Models\TreatmentType;
use App\Models\Price;

class TreatmentController extends Controller
{
    public function index(Request $request)
    {
        $query = TreatmentPlan::with(['patient', 'doctor']);
        
        if ($request->has('patient_search') && $request->patient_search) {
            $search = $request->patient_search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $treatmentPlans = $query->latest()->paginate(15);
        
        return view('treatments.index', compact('treatmentPlans'));
    }
    
    public function create()
    {
        return view('treatments.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatments' => 'required|array|min:1',
            'treatments.*.tooth' => 'required|integer|between:11,48',
            'treatments.*.treatmentType' => 'required|string',
            'treatments.*.cost' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0'
        ]);
        
        $patient = Patient::find($request->patient_id);
        
        $treatmentPlan = TreatmentPlan::create([
            'patient_id' => $patient->id,
            'doctor_id' => Auth::id(),
            'title' => 'طرح درمان ' . $patient->full_name,
            'total_cost' => $request->total_cost,
            'status' => 'active'
        ]);
        
        foreach ($request->treatments as $treatment) {
            TreatmentItem::create([
                'treatment_plan_id' => $treatmentPlan->id,
                'tooth_number' => $treatment['tooth'],
                'treatment_type_id' => $treatment['treatmentType'],
                'treatment_type' => $treatment['treatmentText'],
                'cost' => $treatment['cost'],
                'description' => $treatment['notes'] ?? null,
                'status' => 'pending'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'طرح درمان با موفقیت ایجاد شد.',
            'redirect' => route('treatments.show', $treatmentPlan)
        ]);
    }

    public function show(TreatmentPlan $treatment)
    {
        $treatment->load(['patient', 'doctor', 'treatmentItems.treatmentType', 'payments']);
        
        return view('treatments.show', compact('treatment'));
    }
    
    public function getPatientTreatments(Request $request)
    {
        $patientId = $request->patient_id;
        
        if (!$patientId) {
            return response()->json([
                'success' => false,
                'message' => 'شناسه بیمار ارسال نشده'
            ]);
        }
        
        // پیدا کردن آخرین طرح درمان فعال بیمار
        $treatmentPlan = TreatmentPlan::where('patient_id', $patientId)
            ->where('status', 'active')
            ->with('treatmentItems')
            ->latest()
            ->first();
            
        if (!$treatmentPlan) {
            return response()->json([
                'success' => true,
                'has_treatment' => false,
                'message' => 'طرح درمان فعالی برای این بیمار یافت نشد'
            ]);
        }
        
        // تبدیل درمان‌ها به فرمت مورد نیاز
        $treatments = $treatmentPlan->treatmentItems->map(function($item) {
            // تبدیل شماره دندان به فرمت نمایشی
            $quadrant = floor($item->tooth_number / 10);
            $position = $item->tooth_number % 10;
            
            switch($quadrant) {
                case 1: $toothDescription = $position . ' بالا چپ'; break;
                case 2: $toothDescription = $position . ' بالا راست'; break;
                case 3: $toothDescription = $position . ' پایین راست'; break;
                case 4: $toothDescription = $position . ' پایین چپ'; break;
                default: $toothDescription = $item->tooth_number;
            }
            
            // تبدیل به اعداد فارسی
            $persianNumber = preg_replace_callback('/\d/', function($matches) {
                $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                return $persianDigits[intval($matches[0])];
            }, $toothDescription);
            
            return [
                'tooth' => $item->tooth_number,
                'toothDescription' => 'دندان ' . $persianNumber,
                'treatmentType' => $item->treatment_type_id,
                'treatmentText' => $item->treatment_type,
                'cost' => $item->cost,
                'notes' => $item->description
            ];
        });
        
        return response()->json([
            'success' => true,
            'has_treatment' => true,
            'treatment_plan_id' => $treatmentPlan->id,
            'treatments' => $treatments,
            'total_cost' => $treatmentPlan->total_cost
        ]);
    }
}