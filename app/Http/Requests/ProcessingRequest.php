<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'amount_paid' => 'required|numeric|min:0',
            'over_payment' => 'required|numeric',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
