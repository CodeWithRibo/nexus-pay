<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use App\Models\UserInformation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function index()
    {
        return Inertia::render('Register');
    }

    public function register(AuthRequest $request)
    {
        $validated = $request->validated();

        if ($validated)
        {
            $inputBirthDate = $request->birth_date;
            $getDate= Carbon::parse($inputBirthDate)->format('Ymd');
            $generateDefPassword = $request->last_name.$getDate;

             $user = User::query()
                ->create([
                    'student_id' => $request->role === 'student' ? $request->student_id : null,
                    'email' => $request->email,
                    'password' => $generateDefPassword,
                    'role' => $request->role,
                ]);
            UserInformation::query()
                ->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'address' => $request->address,
                    'phone_number' => $request->phone_number,
                    'birth_date' => $request->birth_date,
                    'user_id' => $user->id,
                ]);
        }
        return to_route('auth.index');
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
