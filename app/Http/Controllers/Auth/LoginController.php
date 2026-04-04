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

            return redirect()->intended(route('kiosk.service-selection'));
        }
        return back()->withErrors([
            'login' => 'Invalid credentials',
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }

    public function destroyWithTransaction(Request $request)
    {
        $transactionId = $request->session()->get('transaction_id');

        if ($transactionId) {
            $payment = Payment::query()
                ->where('transaction_id', $transactionId)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->first();

            if ($payment) {
                $payment->delete();
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
