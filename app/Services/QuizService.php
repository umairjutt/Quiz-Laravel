<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Carbon\Carbon;


class QuizService
{
    public function assignQuiz($quizId, $studentIds)
    {
        $quiz = Quiz::findOrFail($quizId);
        $activateAt = Carbon::now()->addDays(2);
        $expiresAt = Carbon::now()->addDays(7);

        foreach ($studentIds as $studentId) {
            QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $studentId,
                'activate_at' => $activateAt,
                'expires_at' => $expiresAt,
            ]);
        }

        return ['message' => 'Quiz assigned to selected students.'];
    }

    public function createQuiz($data)
    {
        $quiz = Quiz::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'activate_at' => $data['activate_at'],
            'expires_at' => $data['expires_at'],
        ]);

        foreach ($data['questions'] as $question) {
            $quiz->questions()->create([
                'question_text' => $question['question_text'],
                'correct_answer' => $question['correct_answer'],
                'options' => json_encode($question['options']),
            ]);
        }

        return $quiz->load('questions');
    }

    public function updateQuiz($quiz, $data)
    {
        $quiz->update($data);

        if (isset($data['questions'])) {
            foreach ($data['questions'] as $questionData) {
                $quiz->questions()->updateOrCreate(
                    ['question_text' => $questionData['question_text']],
                    [
                        'correct_answer' => $questionData['correct_answer'],
                        'options' => json_encode($questionData['options']),
                    ]
                );
            }
        }

        return $quiz->load('questions');
    }

    public function deleteQuiz($quiz)
    {
        $quiz->delete();
    }

    public function listQuizzes()
    {
        return Quiz::all();
    }
}
