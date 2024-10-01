<?php

namespace App\Services;

use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;


class StudentService
{
    // View assigned quizzes
    public function viewAssignedQuizzes()
    {
        $quizzes = QuizAttempt::where('student_id', Auth::id())
            ->where('activate_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->with('quiz')
            ->get();

        return $quizzes;
    }

    // Attempt quiz and upload video
    public function attemptQuiz($data, $quizAttemptId)
    {
        $quizAttempt = QuizAttempt::where('id', $quizAttemptId)
            ->where('student_id', Auth::id())
            ->firstOrFail();

        if ($quizAttempt->attempted_at) {
            return ['error' => 'Quiz already attempted.', 'status' => 400];
        }

        // Store the video
        if (isset($data['video'])) {
            $path = $data['video']->store('videos', 'public');
            $quizAttempt->video_path = $path;
        }

        $quizAttempt->attempted_at = now();
        // Implement your score calculation logic here
        $quizAttempt->score = 0; // Placeholder for now
        $quizAttempt->save();

        return ['message' => 'Quiz attempted successfully.', 'status' => 200];
    }

    
}
