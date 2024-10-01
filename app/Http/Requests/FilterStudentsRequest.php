<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterStudentsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your auth logic.
    }

    public function rules()
    {
        return [
            'status' => 'sometimes|in:approved,rejected,pending',
        ];
    }
}
