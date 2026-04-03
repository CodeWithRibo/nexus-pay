<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
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
            $request->session()->regenerate();

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
}
