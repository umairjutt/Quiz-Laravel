<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'activate_at' => 'sometimes|required|date|after:now',
            'expires_at' => 'sometimes|required|date|after:activate_at',
            'questions' => 'sometimes|required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.correct_answer' => 'required|string',
            'questions.*.options' => 'required|array',
        ];
    }
}
