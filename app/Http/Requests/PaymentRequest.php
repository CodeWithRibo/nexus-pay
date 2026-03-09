<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reference_no' => ['required'],
            'amount_paid' => ['required', 'decimal:2'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
