<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;

use App\Http\Requests\AttemptQuizRequest;
use App\Services\StudentService;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    // View Assigned Quizzes
    public function viewQuizzes()
    {
    try {
        $quizzes = $this->studentService->viewAssignedQuizzes();
        return response()->json(['data' => $quizzes, 'message' => 'Quizzes retrieved successfully.'], 200);
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Error retrieving quizzes.'], 500);
    }
    }


    // Attempt Quiz with Video Upload
    // public function attemptQuiz(AttemptQuizRequest $request, $id)
    // {
    //     $result = $this->studentService->attemptQuiz($request->validated(), $id);

    //     if (isset($result['error'])) {
    //         return response()->json(['message' => $result['error']], $result['status']);
    //     }

    //     return response()->json(['message' => $result['message']], $result['status']);
    // }

    public function attemptQuiz(AttemptQuizRequest $request, $id)
{
    try {
        // Check if the quiz is already attempted
        $quizAttempt = QuizAttempt::where('id', $id)
            ->where('student_id', Auth::id())
            ->firstOrFail();

        // Check if quiz has already been completed or expired
        if ($quizAttempt->status === 'completed' || $quizAttempt->status === 'expired') {
            return response()->json(['message' => 'You have already completed or missed this quiz.'], 400);
        }

        // Check if quiz is expired (time-based condition)
        if (now()->greaterThan($quizAttempt->end_time)) {
            $quizAttempt->status = 'expired';
            $quizAttempt->save();
            return response()->json(['message' => 'Quiz expired.'], 400);
        }

        // Update quiz status to 'started' if not already started
        if ($quizAttempt->status !== 'started') {
            $quizAttempt->status = 'started';
            $quizAttempt->save();
        }

        // Call the service to handle quiz attempt logic
        $result = $this->studentService->attemptQuiz($request->validated(), $id);

        // Return an error if the service encounters one
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        // On successful quiz completion, change the status to 'completed'
        if ($result['status'] === 200 && $result['completed'] === true) {
            $quizAttempt->status = 'completed';
            $quizAttempt->save();
        }

        // Return the result of the quiz attempt
        return response()->json(['message' => $result['message']], $result['status']);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Quiz attempt not found.'], 404);
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Error attempting quiz.'], 500);
    }
}


}
