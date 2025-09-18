<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ], [
            'phone.required' => 'وارد کردن شماره تلفن الزامی است.',
            'password.required' => 'وارد کردن رمز عبور الزامی است.',
        ]);
        
        // پیدا کردن کاربر با شماره تلفن
        $user = User::where('phone', $request->phone)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'phone' => 'شماره تلفن یا رمز عبور اشتباه است.',
            ]);
        }
        
        // بررسی فعال بودن حساب
        if (isset($user->is_active) && !$user->is_active) {
            return back()->withErrors(['phone' => 'حساب کاربری شما غیرفعال است.']);
        }
        
        Auth::login($user);
        $request->session()->regenerate();
        
        return redirect()->intended('dashboard')
            ->with('success', 'با موفقیت وارد شدید. خوش آمدید ' . $user->name . '!');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}