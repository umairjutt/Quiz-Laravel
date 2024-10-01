<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Quiz;
use App\Helpers\Helpers;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Console\Scheduling\Schedule;

class QuizController extends Controller
{
    // Assign Quiz to Students (Admin and Manager)
    public function assignQuiz(Request $request)
    {
    try {
        $validator = Validator::make($request->all(), [
            'quiz_id' => 'required|exists:quizzes,id',
            'student_ids' => 'required|string',
            'student_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $quiz = Quiz::findOrFail($request->quiz_id);

        $activateAt = Carbon::now()->addDays(2);  // Set activation date
        $expiresAt = Carbon::now()->addDays(7);   // Set expiration date

        foreach (json_decode($request->student_ids) as $studentId) {
            QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $studentId,
                'activate_at' => $activateAt,
                'expires_at' => $expiresAt,
            ]);
        }

        return Helpers::success([], 'Quiz assigned to selected students.');
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Error in assigning quiz'], 500);
    }
    }


    protected function schedule(Schedule $schedule)
    {
        $schedule->command('quiz:update-status')->everyMinute();
    }

    public function createQuiz(Request $request)
    {
    try {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activate_at' => 'required|date|after:now',
            'expires_at' => 'required|date|after:activate_at',
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.correct_answer' => 'required|string',
            'questions.*.options' => 'required|array', // Options must be an array
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'activate_at' => $request->activate_at,
            'expires_at' => $request->expires_at,
        ]);

        // Store questions
        foreach ($request->questions as $question) {
            $quiz->questions()->create([
                'question_text' => $question['question_text'],
                'correct_answer' => $question['correct_answer'],
                'options' => json_encode($question['options']),
            ]);
        }

        return Helpers::success(['quiz' => $quiz->load('questions')], 'Quiz created successfully.', 201);
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Error in creating quiz'], 500);
    }
    }

    
            // Update Quiz Method
    public function updateQuiz(Request $request, $id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'activate_at' => 'sometimes|required|date|after:now',
                'expires_at' => 'sometimes|required|date|after:activate_at',
                'questions' => 'sometimes|required|array',
                'questions.*.question_text' => 'required|string',
                'questions.*.correct_answer' => 'required|string',
                'questions.*.options' => 'required|array',
            ]);
            
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            
            $quiz->update($request->only(['title', 'description', 'activate_at', 'expires_at']));
            
            if ($request->has('questions')) {
                foreach ($request->questions as $questionData) {
                    $quiz->questions()->updateOrCreate(
                        ['question_text' => $questionData['question_text']],
                        [
                                    'correct_answer' => $questionData['correct_answer'],
                                    'options' => json_encode($questionData['options']),
                        ]
                    );
                }
            }
            
            return Helpers::success(['quiz' => $quiz->load('questions')], 'Quiz updated successfully.');
        } catch (\Exception $e) {
            \App\Helpers\Helpers::logError($e); // Log error to DB
            return response()->json(['message' => 'Error in updating quiz'], 500);
        }
    }
            

    // Delete Quiz Method

    public function deleteQuiz($id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            $quiz->delete();
            return Helpers::success([], 'Quiz deleted successfully.');
        } catch (\Exception $e) {
            \App\Helpers\Helpers::logError($e); // Log error to DB
            return response()->json(['message' => 'Error in deleting quiz'], 500);
        }
    }
    
            // View All Quizez
    public function listQuizzes()
    {
        try {
            $quizzes = Quiz::all();
            return Helpers::success($quizzes);
        } catch (\Exception $e) {
            \App\Helpers\Helpers::logError($e); // Log error to DB
            return response()->json(['message' => 'Error in listing quizzes'], 500);
        }
    }
            

    public function calculateScore(QuizAttempt $attempt)
    {
        try {
            // Implement actual scoring logic
            $attempt->score = rand(0, 100); // Placeholder
            $attempt->save();
        } catch (\Exception $e) {
            \App\Helpers\Helpers::logError($e); // Log error to DB
            return response()->json(['message' => 'Error in calculating score'], 500);
        }
    }
    
}