<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'estimated_time' => 'required|integer',
            'used_time' => 'nullable|integer',
        ];
    }
}
