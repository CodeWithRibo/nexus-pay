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
    public function create(Request $request)
    {
        $role = $this->resolveRole($request);

        return Inertia::render($role === 'admin' ? 'admin/Login' : 'Login', [
            'role' => $role,
        ]);
    }

    public function store(LoginRequest $request)
    {
        $role = $this->resolveRole($request);
        $login = $request->input('login');
        $password = $request->input('password');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        if (Auth::guard('web')->attempt([$fieldType => $login, 'password' => $password, 'role' => $role])) {
            $transactionId = $request->session()->get('transaction_id');

            $request->session()->regenerate();

            if ($transactionId) {
                $request->session()->put('transaction_id', $transactionId);
            }

            $user = Auth::user()->loadMissing('information');
            $getName = $user->information;

            $fullName = trim(($getName?->first_name ?? '').' '.($getName?->last_name ?? ''));
            $displayName = $fullName !== '' ? $fullName : $user->email;

            $request->session()->flash('success', 'Welcome Back! '.$displayName);

            if ($role === 'admin') {
                $request->session()->forget('url.intended');

                return to_route('admin.dashboard');
            }

            return redirect()->intended(route('kiosk.service-selection'));
        }

        return back()->withErrors([
            'login' => $role === 'admin'
                ? 'Invalid email or password'
                : 'Invalid Student ID / Email or password',
        ]);
    }

    public function destroy(Request $request)
    {
        $role = strtolower((string) Auth::user()?->role);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->flash('success', 'You have been safely logged out');

        return to_route($role === 'admin' ? 'admin.login' : 'login');
    }

    private function resolveRole(Request $request): string
    {
        $role = $request->route('role', 'student');

        return in_array($role, ['student', 'admin'], true) ? $role : 'student';
    }

}
