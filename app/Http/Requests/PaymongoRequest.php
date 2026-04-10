<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymongoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'context' => ['required', Rule::in(['tuition', 'dynamic'])],
            'balance_id' => ['nullable', 'integer'],
            'pay_all' => ['nullable', 'boolean'],
            'use_overpayment' => ['nullable', 'boolean'],
            'paymongo_method' => ['nullable', Rule::in(['qrph', 'gcash', 'paymaya'])],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
