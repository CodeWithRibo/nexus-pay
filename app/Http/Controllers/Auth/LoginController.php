<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Login');
    }

    public function store(LoginRequest $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        if (Auth::guard('web')->attempt([$fieldType => $login, 'password' => $password])) {
            $transactionId = $request->session()->get('transaction_id');

            $request->session()->regenerate();

            if ($transactionId) {
                $request->session()->put('transaction_id', $transactionId);
            }

            $getName = Auth::user()->load('information')->information;

            $fullName  = $getName?->first_name . ' ' . $getName?->last_name;
            $request->session()->flash('success', 'Welcome Back!' . " {$fullName}");
            return redirect()->intended(route('kiosk.service-selection'));
        }
        return back()->withErrors([
            'login' => 'Invalid Student ID / Email or password',
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->flash('success', 'You have been safely logged out');
        return to_route('login');
    }

}
