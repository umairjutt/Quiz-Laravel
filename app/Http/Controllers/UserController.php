<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Requests\CreateManagerRequest;
use App\Http\Requests\FilterStudentsRequest;
use App\Services\UserService;


class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Admin adds a Manager
    public function createManager(CreateManagerRequest $request)
    {
    try {
        $result = $this->userService->createManager($request->validated());
        return response()->json($result, 201);
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log the error to the database
        return response()->json(['message' => 'Failed to create manager.'], 500);
    }
    }


    // Admin lists Students
    public function listStudents()
    {
    try {
        $students = $this->userService->listStudents();
        return response()->json($students);
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Failed to retrieve students.'], 500);
    }
    }


    // Admin approves a Student
    public function approveStudent($id)
    {
    try {
        $result = $this->userService->approveStudent($id);
        return response()->json($result);
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Failed to approve student.'], 500);
    }
    }


    // Admin rejects a Student
    public function rejectStudent($id)
    {
    try {
        $result = $this->userService->rejectStudent($id);
        return response()->json($result);
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Failed to reject student.'], 500);
    }
    }


    // Filter Students
    public function filterStudents(FilterStudentsRequest $request)
    {
    try {
        $students = $this->userService->filterStudents($request->status);
        return response()->json($students);
    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB
        return response()->json(['message' => 'Failed to filter students.'], 500);
    }
    }

}
