<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\UserInformation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterController extends Controller
{

    public function create()
    {
        return Inertia::render('Register');
    }

    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        if ($validated)
        {
            $birthDate = $request->birth_date;
            $timestamp = Carbon::create($birthDate['year'],$birthDate['month'],$birthDate['day'])->format('Y-m-d');

            $dateString = "{$birthDate['year']}-{$birthDate['month']}-{$birthDate['day']}";
            $formattedDate = Carbon::parse($dateString)->format('Ymd');

            $lastName =  str_replace(' ', '', strtolower($request->last_name));
            $defaultPass = $lastName . $formattedDate;

            $user = User::query()
                ->create([
                    'student_id' => $request->role === 'student' ? $request->student_id : null,
                    'email' => $request->email,
                    'password' => $defaultPass,
                    'role' => $request->role,
                ]);
            UserInformation::query()
                ->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'address' => $request->address,
                    'phone_number' => $request->phone_number,
                    'birth_date' => $timestamp,
                    'user_id' => $user->id,
                ]);
        }
        return to_route('home');
    }
}
