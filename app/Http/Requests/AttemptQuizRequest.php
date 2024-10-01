<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttemptQuizRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to true for now. Adjust if needed.
    }

    public function rules()
    { 
        return [
            //'quiz_id'=> 'required',
            //'student_id'=>'required',
            //'users'=>'required',
            //'attempted_at'=>'required',
            //'score'=>'required',
            'video' => 'required|mimes:mp4,avi,mov|max:20480', // Max 20MB
        ];
    }
}
