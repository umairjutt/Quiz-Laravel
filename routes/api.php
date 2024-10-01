<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;

// Public Routes
Route::post('/register', [AuthController::class, 'registerStudent']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password-reset', [AuthController::class, 'passwordReset'])->name('password.reset');
Route::post('/password-reset/confirm', [AuthController::class, 'passwordResetConfirm']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/quiz', [QuizController::class, 'createQuiz']);
Route::put('/quiz/{id}', [QuizController::class, 'updateQuiz']);

//Manager
Route::middleware(['auth:api'])->post('/create-manager', [UserController::class, 'createManager']);
//Student
Route::middleware(['auth:api'])->get('/students', [UserController::class, 'listStudents']);
Route::middleware(['auth:api'])->post('/students/{id}/approve', [UserController::class, 'approveStudent']);
Route::middleware(['auth:api'])->post('/students/{id}/reject', [UserController::class, 'rejectStudent']);
//Quiz
Route::middleware(['auth:api'])->post('/assign-quiz', [QuizController::class, 'assignQuiz']);

Route::middleware(['auth:api'])->get('/quizzes', [StudentController::class, 'viewQuizzes']);



// Protected Routes
Route::middleware(['auth:api'])->group(function () {
    
    // Admin Routes
    Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
        // Route::post('/create-manager', [UserController::class, 'createManager']);
        // Route::get('/students', [UserController::class, 'listStudents']);
        //Route::post('/students/{id}/approve', [UserController::class, 'approveStudent']);
        //Route::post('/students/{id}/reject', [UserController::class, 'rejectStudent']);
        //Route::post('/assign-quiz', [QuizController::class, 'assignQuiz']);
        
        
        // Additional admin routes...
    });

    // Manager Routes
    Route::middleware(['role:Manager'])->prefix('manager')->group(function () {
        Route::post('/assign-quiz', [QuizController::class, 'assignQuiz']);
        Route::get('/students', [UserController::class, 'listStudents']);
        // Additional manager routes...
    });

    // Student Routes
    Route::middleware(['role:Student'])->prefix('student')->group(function () {
       // Route::get('/quizzes', [StudentController::class, 'viewQuizzes']);
        Route::post('/quizzes/{id}/attempt', [StudentController::class, 'attemptQuiz']);
        // Additional student routes...
    });

    // Admin Routes
Route::middleware(['auth:api', 'role:Admin'])->prefix('admin')->group(function () {
    // ... existing routes ...
    Route::get('/filter-students', [UserController::class, 'filterStudents']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/quizzes/{id}/results', [QuizController::class, 'calculateScore']);
});


});
