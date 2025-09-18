<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Helpers\GenderDetector;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('national_code', 'like', "%{$search}%");
            });
        }
        
        $patients = $query->latest()->paginate(20);
        
        return view('patients.index', compact('patients'));
    }
    
    public function create()
    {
        return view('patients.create');
    }
    
    public function store(Request $request)
    {
        // تبدیل name به first_name و last_name
        if ($request->has('name')) {
            $nameParts = explode(' ', $request->name, 2);
            $request->merge([
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? $nameParts[0] // اگه فقط یک کلمه بود
            ]);
        }
        
        // تشخیص خودکار جنسیت بر اساس نام اول
        if ($request->filled('first_name') && !$request->filled('gender')) {
            $detectedGender = GenderDetector::detectGender($request->first_name);
            if ($detectedGender) {
                $request->merge(['gender' => $detectedGender]);
            }
        }
        
        // Handle Persian date conversion
        if ($request->filled('registered_at')) {
            $request->merge(['registered_at' => $this->convertPersianToGregorian($request->registered_at)]);
        }
        
        if ($request->filled('birth_date')) {
            $request->merge(['birth_date' => $this->convertPersianToGregorian($request->birth_date)]);
        }
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|unique:patients,phone',
            'phone2' => 'nullable|string|regex:/^09\d{9}$/',
            'file_number' => 'nullable|numeric',
            'national_code' => 'nullable|string',
            'birth_date' => 'nullable|date', // yyyy-mm-dd
            'registered_at' => 'nullable|date', // yyyy-mm-dd
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);
        
        // اگر registered_at خالی بود، امروز ست شود (fallback بک‌اند)
        if (empty($validated['registered_at'])) {
            $validated['registered_at'] = now()->toDateString();
        }
        
        $patient = Patient::create($validated);
        
        return redirect()->route('patients.index')
            ->with('success', 'بیمار با موفقیت ثبت شد.');
    }
    
    public function show(Patient $patient)
    {
        $patient->load(['treatmentPlans', 'appointments' => function($q) {
            $q->latest();
        }]);
        
        return view('patients.show', compact('patient'));
    }
    
    public function searchPatients(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $patients = Patient::where(function($q) use ($query) {
            $q->where('first_name', 'LIKE', "%{$query}%")
              ->orWhere('last_name', 'LIKE', "%{$query}%")
              ->orWhere('phone', 'LIKE', "%{$query}%")
              ->orWhere('national_code', 'LIKE', "%{$query}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"]);
        })
        ->select('id', 'first_name', 'last_name', 'phone', 'national_code')
        ->limit(10)
        ->get()
        ->map(function($patient) {
            return [
                'id' => $patient->id,
                'full_name' => $patient->full_name,
                'phone' => $patient->phone ?? '',
                'national_code' => $patient->national_code ?? ''
            ];
        });
        
        return response()->json($patients);
    }
    
    public function quickCreatePatient(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|regex:/^09\d{9}$/|unique:patients,phone',
        ], [
            'first_name.required' => 'نام الزامی است',
            'last_name.required' => 'نام خانوادگی الزامی است',
            'phone.required' => 'شماره تماس الزامی است',
            'phone.regex' => 'فرمت شماره تماس صحیح نیست (مثال: 09123456789)',
            'phone.unique' => 'این شماره تماس قبلاً ثبت شده است',
        ]);
        
        // اضافه کردن تاریخ ثبت امروز
        $validated['registered_at'] = now()->toDateString();
        
        $patient = Patient::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'بیمار با موفقیت ایجاد شد',
            'patient' => [
                'id' => $patient->id,
                'full_name' => $patient->full_name,
                'phone' => $patient->phone,
            ]
        ]);
    }
    
    // تابع کمکی برای تبدیل تاریخ شمسی به میلادی
    private function convertPersianToGregorian($persianDate)
    {
        if (!$persianDate) return null;
        
        // جدا کردن اجزای تاریخ شمسی
        $parts = explode('/', $persianDate);
        if (count($parts) != 3) return null;
        
        [$year, $month, $day] = $parts;
        
        // تبدیل شمسی به میلادی
        $gregorian = $this->jalaliToGregorian($year, $month, $day);
        if (!$gregorian) return null;
        
        return $gregorian[0] . '-' . str_pad($gregorian[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($gregorian[2], 2, '0', STR_PAD_LEFT);
    }
    
    /**
     * Detect gender based on first name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detectGender(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100'
        ]);
        
        $gender = GenderDetector::detectGender($request->first_name);
        
        return response()->json([
            'gender' => $gender
        ]);
    }
    
    // تبدیل تاریخ شمسی به میلادی
    private function jalaliToGregorian($jy, $jm, $jd)
    {
        $jy = (int)$jy;
        $jm = (int)$jm;
        $jd = (int)$jd;
        
        if ($jy <= 979) {
            $gy = 621;
        } else {
            $gy = 1600;
            $jy -= 979;
        }
        
        $days = (365 * $jy) + ((int)($jy / 33) * 8) + ((int)(($jy % 33 + 3) / 4)) + $jd + 78;
        
        if ($jm < 7) {
            $days += ($jm - 1) * 31;
        } else {
            $days += (($jm - 7) * 30) + 186;
        }
        
        $gy += 400 * ((int)($days / 146097));
        $days %= 146097;
        
        if ($days > 36524) {
            $gy += 100 * ((int)(--$days / 36524));
            $days %= 36524;
            
            if ($days >= 365) $days++;
        }
        
        $gy += 4 * ((int)($days / 1461));
        $days %= 1461;
        
        if ($days > 365) {
            $gy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        
        $gd = $days + 1;
        
        $sal_a = [0, 31, (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $gm = 0;
        
        for ($i = 0; $gm < 12 && $gd > $sal_a[$gm]; $i++) {
            $gd -= $sal_a[$gm++];
        }
        
        return [$gy, $gm, $gd];
    }
}