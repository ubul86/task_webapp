<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUsedTimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'used_time' => 'nullable|integer',
        ];
    }
}
