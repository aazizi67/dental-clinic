<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\ChartOfAccount;
use App\Models\ExpenseCategory;
use App\Models\Patient;
use App\Models\Expense;
use Carbon\Carbon;

class AccountingController extends Controller
{
    // نمایش داشبورد حسابداری
    public function index()
    {
        $today = now();
        $thisMonth = now()->startOfMonth();
        
        $stats = [
            'today_income' => Transaction::where('type', 'income')
                ->whereDate('transaction_date', $today)
                ->sum('amount'),
            'today_expense' => Transaction::where('type', 'expense')
                ->whereDate('transaction_date', $today)
                ->sum('amount'),
            'month_income' => Transaction::where('type', 'income')
                ->where('transaction_date', '>=', $thisMonth)
                ->sum('amount'),
            'month_expense' => Transaction::where('type', 'expense')
                ->where('transaction_date', '>=', $thisMonth)
                ->sum('amount'),
        ];
        
        $stats['today_profit'] = $stats['today_income'] - $stats['today_expense'];
        $stats['month_profit'] = $stats['month_income'] - $stats['month_expense'];
        
        // آخرین تراکنش‌ها
        $recentTransactions = Transaction::with(['patient', 'account'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('accounting.index', compact('stats', 'recentTransactions'));
    }
    
    // لیست تراکنش‌ها
    public function transactions(Request $request)
    {
        $query = Transaction::with(['patient', 'account', 'creator']);
        
        // فیلتر بر اساس تاریخ
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }
        
        // فیلتر بر اساس نوع
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // فیلتر بر اساس دسته بندی
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')
                             ->paginate(20);
        
        return view('accounting.transactions', compact('transactions'));
    }
    
    // فرم ایجاد تراکنش جدید
    public function createTransaction()
    {
        $accounts = ChartOfAccount::where('is_active', true)
                                 ->where('level', 2)
                                 ->orderBy('code')
                                 ->get();
        
        $expenseCategories = ExpenseCategory::where('is_active', true)->get();
        
        return view('accounting.create-transaction', compact('accounts', 'expenseCategories'));
    }
    
    // ذخیره تراکنش جدید
    public function storeTransaction(Request $request)
    {
        $rules = [
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|in:patient_payment,dental_materials,equipment,laboratory,other',
            'amount' => 'required|numeric|min:0',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'payment_method' => 'required|in:cash,card,pos,bank_transfer,check',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ];
        
        // قوانین اضافی برای چک
        if ($request->payment_method === 'check') {
            $rules['check_number'] = 'required|string|max:50';
            $rules['check_date'] = 'required|date';
            $rules['check_bank'] = 'required|string|max:100';
            $rules['sayad_id'] = 'required|string|max:50';
        }
        
        // قوانین اضافی برای پرداخت بیمار
        if ($request->category === 'patient_payment') {
            $rules['patient_id'] = 'required|exists:patients,id';
            $rules['treatment_plan_id'] = 'nullable|exists:treatment_plans,id';
        }
        
        $validated = $request->validate($rules);
        
        // تولید شماره تراکنش
        $validated['transaction_number'] = Transaction::generateTransactionNumber();
        $validated['created_by'] = auth()->id();
        
        // وضعیت چک
        if ($request->payment_method === 'check') {
            $validated['check_status'] = 'received';
        }
        
        Transaction::create($validated);
        
        return redirect()->route('accounting.transactions')
                        ->with('success', 'تراکنش با موفقیت ثبت شد');
    }
    
    // طرح حساب‌ها
    public function chartOfAccounts()
    {
        $accounts = ChartOfAccount::with('parent', 'children')
                                 ->orderBy('code')
                                 ->get();
        
        return view('accounting.chart-of-accounts', compact('accounts'));
    }
    
    // گزارشات مالی
    public function reports(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        // گزارش درآمد و هزینه
        $incomeByCategory = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        $expenseByCategory = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        // گزارش روزانه
        $dailyReport = Transaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->selectRaw('transaction_date, type, SUM(amount) as total')
            ->groupBy('transaction_date', 'type')
            ->orderBy('transaction_date')
            ->get();
        
        return view('accounting.reports', compact(
            'incomeByCategory',
            'expenseByCategory', 
            'dailyReport',
            'dateFrom',
            'dateTo'
        ));
    }
    
    // مدیریت هزینه‌ها
    public function expenses(Request $request)
    {
        $query = Expense::query()->with('creator')
            ->byCategory($request->category)
            ->byDateRange($request->from_date, $request->to_date)
            ->byAmountRange($request->min_amount, $request->max_amount)
            ->orderBy('created_at', 'desc');

        $expenses = $query->paginate(15);
        
        // آمار هزینه‌ها
        $todayExpenses = Expense::whereDate('created_at', today())->sum('amount');
        $monthExpenses = Expense::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $dentalMaterialsExpenses = Expense::where('category', 'dental_materials')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $equipmentExpenses = Expense::where('category', 'equipment')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        return view('accounting.expenses', compact(
            'expenses',
            'todayExpenses',
            'monthExpenses',
            'dentalMaterialsExpenses',
            'equipmentExpenses'
        ));
    }

    public function storeExpense(Request $request)
    {
        $rules = [
            'category' => 'required|in:dental_materials,equipment,laboratory,rent,utilities,marketing,other',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,check,bank_transfer',
            'status' => 'required|in:paid,pending,cancelled',
            'notes' => 'nullable|string|max:1000'
        ];

        // اعتبارسنجی اضافی برای چک
        if ($request->payment_method === 'check') {
            $rules['check_number'] = 'required|string|max:50';
            $rules['check_date'] = 'required|date';
            $rules['sayad_id'] = 'required|string|max:50';
        }

        $validated = $request->validate($rules);
        $validated['created_by'] = auth()->id();

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'هزینه با موفقیت ثبت شد',
            'expense' => $expense
        ]);
    }

    public function showExpense(Expense $expense)
    {
        return response()->json($expense);
    }

    public function updateExpense(Request $request, Expense $expense)
    {
        $rules = [
            'category' => 'required|in:dental_materials,equipment,laboratory,rent,utilities,marketing,other',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,check,bank_transfer',
            'status' => 'required|in:paid,pending,cancelled',
            'notes' => 'nullable|string|max:1000'
        ];

        if ($request->payment_method === 'check') {
            $rules['check_number'] = 'required|string|max:50';
            $rules['check_date'] = 'required|date';
            $rules['sayad_id'] = 'required|string|max:50';
        }

        $validated = $request->validate($rules);
        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'هزینه با موفقیت به‌روزرسانی شد',
            'expense' => $expense
        ]);
    }

    public function destroyExpense(Expense $expense)
    {
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'هزینه با موفقیت حذف شد'
        ]);
    }
    
    // تحلیل مالی پیشرفته
    public function analytics(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : now()->subDays(30);
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : now();
        $analysisType = $request->analysis_type ?? 'daily';
        
        // محاسبه KPIها
        $totalIncome = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->sum('amount');
            
        $totalExpenses = Expense::whereBetween('created_at', [$fromDate, $toDate])
            ->sum('amount');
            
        $netProfit = $totalIncome - $totalExpenses;
        $profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;
        $dailyAverage = $fromDate->diffInDays($toDate) > 0 ? $totalIncome / $fromDate->diffInDays($toDate) : 0;
        
        // محاسبه رشد نسبت به دوره قبل
        $previousPeriodStart = $fromDate->copy()->subDays($fromDate->diffInDays($toDate));
        $previousPeriodEnd = $fromDate->copy()->subDay();
        
        $previousIncome = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('amount');
            
        $previousExpenses = Expense::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('amount');
            
        $incomeGrowth = $previousIncome > 0 ? (($totalIncome - $previousIncome) / $previousIncome) * 100 : 0;
        $expenseGrowth = $previousExpenses > 0 ? (($totalExpenses - $previousExpenses) / $previousExpenses) * 100 : 0;
        
        // داده‌های نمودار
        $chartLabels = [];
        $revenueData = [];
        $expenseData = [];
        
        if ($analysisType === 'daily') {
            $period = $fromDate->copy();
            while ($period->lte($toDate)) {
                $chartLabels[] = $period->format('m/d');
                
                $dayRevenue = Transaction::where('type', 'income')
                    ->whereDate('transaction_date', $period)
                    ->sum('amount');
                $revenueData[] = $dayRevenue;
                
                $dayExpense = Expense::whereDate('created_at', $period)
                    ->sum('amount');
                $expenseData[] = $dayExpense;
                
                $period->addDay();
            }
        }
        
        // توزیع هزینه‌ها
        $expensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('category')
            ->get();
            
        $expenseCategories = [];
        $expenseAmounts = [];
        
        foreach ($expensesByCategory as $expense) {
            $categoryNames = [
                'dental_materials' => 'مواد دندانی',
                'equipment' => 'تجهیزات',
                'laboratory' => 'لابراتوار',
                'rent' => 'اجاره',
                'utilities' => 'آب و برق',
                'marketing' => 'تبلیغات',
                'other' => 'سایر'
            ];
            
            $expenseCategories[] = $categoryNames[$expense->category] ?? $expense->category;
            $expenseAmounts[] = $expense->total;
        }
        
        // پردرآمدترین روزها
        $topRevenueDays = Transaction::selectRaw('DATE(transaction_date) as date, SUM(amount) as revenue, COUNT(DISTINCT patient_id) as patient_count')
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->groupBy('date')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('Y/m/d'),
                    'revenue' => $item->revenue,
                    'patient_count' => $item->patient_count
                ];
            });
            
        // پرهزینه‌ترین دسته‌ها
        $topExpenseCategories = $expensesByCategory->sortByDesc('total')
            ->take(5)
            ->map(function ($expense) use ($totalExpenses) {
                $categoryNames = [
                    'dental_materials' => 'مواد دندانی',
                    'equipment' => 'تجهیزات',
                    'laboratory' => 'لابراتوار',
                    'rent' => 'اجاره',
                    'utilities' => 'آب و برق',
                    'marketing' => 'تبلیغات',
                    'other' => 'سایر'
                ];
                
                return [
                    'name' => $categoryNames[$expense->category] ?? $expense->category,
                    'amount' => $expense->total,
                    'percentage' => $totalExpenses > 0 ? ($expense->total / $totalExpenses) * 100 : 0
                ];
            });
            
        // درآمد بر اساس نوع درمان (فرضی)
        $treatmentTypes = ['عصب‌كشی', 'جرمگیری', 'کامپوزیت', 'ایمپلنت'];
        $treatmentRevenues = [150000, 80000, 120000, 300000];
        
        // روش‌های پرداخت
        $paymentMethods = ['نقدی', 'کارت', 'چک', 'انتقال بانکی'];
        $paymentAmounts = [
            Transaction::where('payment_method', 'cash')->whereBetween('transaction_date', [$fromDate, $toDate])->sum('amount'),
            Transaction::where('payment_method', 'card')->whereBetween('transaction_date', [$fromDate, $toDate])->sum('amount'),
            Transaction::where('payment_method', 'check')->whereBetween('transaction_date', [$fromDate, $toDate])->sum('amount'),
            Transaction::where('payment_method', 'bank_transfer')->whereBetween('transaction_date', [$fromDate, $toDate])->sum('amount')
        ];
        
        $analytics = [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'daily_average' => $dailyAverage,
            'income_growth' => $incomeGrowth,
            'expense_growth' => $expenseGrowth,
            'chart_labels' => $chartLabels,
            'revenue_data' => $revenueData,
            'expense_data' => $expenseData,
            'expense_categories' => $expenseCategories,
            'expense_amounts' => $expenseAmounts,
            'top_revenue_days' => $topRevenueDays,
            'top_expense_categories' => $topExpenseCategories,
            'treatment_types' => $treatmentTypes,
            'treatment_revenues' => $treatmentRevenues,
            'payment_methods' => $paymentMethods,
            'payment_amounts' => $paymentAmounts
        ];
        
        return view('accounting.analytics', compact('analytics'));
    }
    
    // صفحه نسخه پشتیبان و خروجی
    public function backup()
    {
        $stats = [
            'total_transactions' => Transaction::count(),
            'total_expenses' => Expense::count(),
            'total_patients' => Patient::count(),
            'total_accounts' => ChartOfAccount::count()
        ];
        
        // فهرست نسخه‌های پشتیباع (فرضی)
        $backups = [
            [
                'name' => 'نسخه پشتیبان کامل',
                'date' => '۱۴۰۴/۰۶/۲۲',
                'size' => '۲۵ MB',
                'download_url' => '#'
            ]
        ];
        
        return view('accounting.backup', compact('stats', 'backups'));
    }
    
    public function createBackup(Request $request)
    {
        $type = $request->type; // 'full' or 'accounting'
        
        try {
            // شبیه‌سازی تهیه نسخه پشتیبان
            $filename = 'backup_' . $type . '_' . date('Y_m_d_H_i_s') . '.zip';
            
            // در حالت واقعی باید اینجا فایل ZIP بسازید
            
            return response()->json([
                'success' => true,
                'message' => 'نسخه پشتیبان با موفقیت تهیه شد',
                'filename' => $filename,
                'download_url' => '#' // آدرس دانلود فایل
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در تهیه نسخه پشتیبان: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function exportReport(Request $request)
    {
        $reportType = $request->report_type;
        $format = $request->format;
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : null;
        
        try {
            $data = [];
            $filename = '';
            
            switch ($reportType) {
                case 'transactions':
                    $query = Transaction::with(['patient', 'chartOfAccount']);
                    if ($fromDate) $query->where('transaction_date', '>=', $fromDate);
                    if ($toDate) $query->where('transaction_date', '<=', $toDate);
                    $data = $query->get();
                    $filename = 'تراکنش‌ها';
                    break;
                    
                case 'expenses':
                    $query = Expense::with('creator');
                    if ($fromDate) $query->where('created_at', '>=', $fromDate);
                    if ($toDate) $query->where('created_at', '<=', $toDate);
                    $data = $query->get();
                    $filename = 'هزینه‌ها';
                    break;
                    
                case 'income_expense':
                    // آماده‌سازی داده‌های درآمد و هزینه
                    $filename = 'درآمد_و_هزینه';
                    break;
                    
                default:
                    throw new \Exception('نوع گزارش نامعتبر است');
            }
            
            $filename .= '_' . date('Y_m_d') . '.' . ($format === 'excel' ? 'xlsx' : $format);
            
            // شبیه‌سازی فایل Excel
            $content = 'نمونه محتوای گزارش';
            
            return response($content)
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در تهیه گزارش: ' . $e->getMessage()
            ], 500);
        }
    }
}
