<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => [
                'required',
                function ($attribute, $value, $fail) {
                    $role = $this->route('role', 'student');
                    $isEmail = filter_var($value, FILTER_VALIDATE_EMAIL);
                    $isStudentId = preg_match('/^02000[0-9]{6}$/', $value);

                    if ($role === 'admin' && !$isEmail) {
                        $fail('The '.$attribute.' must be a valid email.');
                        return;
                    }

                    if (!$isEmail && !$isStudentId) {
                        $fail('The '.$attribute.' must be a valid email or student ID.');
                    }
                },
            ],
            'password' => ['required'],
        ];

    }

    public function authorize(): bool
    {
        return true;
    }
}
