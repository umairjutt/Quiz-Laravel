<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // You can add logic to authorize the request here.
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cv' => 'required|file|mimes:doc,pdf,csv,docx|max:20480', // Max 20MB
            'phone' => 'required|string|max:20',
        ];
    }
}
