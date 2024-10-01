<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentFormSubmittedNotification;
use Exception;
use App\Helpers\Helpers;

class AuthService
{
    public function registerStudent($data)
{
    try {
        // Handle CV upload
        DB::beginTransaction();
        $cvPath = null;
        if (isset($data['cv'])) {
            $cvPath = $data['cv']->store('cvs', 'public');
        }

        // Create the student user with status 'pending'
        $student = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(10)),
            'cv_path' => $cvPath,
            'phone' => $data['phone'], // Include phone number
            'status' => 'pending', // Set status to pending
        ]);

        // Send email notification to the student
        Mail::to($student->email)->send(new StudentFormSubmittedNotification($student));

        DB::commit();
       
        return Helpers::success([], 'Student registration submitted.');

       // return ['message' => 'Student registration submitted.'];
    } catch (Exception $e) {
        DB::rollBack();
        throw new Exception('Failed to register student.');
    }
}


    public function login($credentials)
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new Exception('Invalid Credentials');
        }

        $user = JWTAuth::user();
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');

        // return [
        //     'message' => 'success',
        //     //'token_type' => 'Bearer',
        //     //'expires_in' => JWTAuth::factory()->getTTL() * 60,
        //     'data' => [
        //         //'name' => $user->name,
        //         //'email' => $user->email,
        //         //'status' => $user->status ?? 'active',
        //         'roles' => $roles,
        //         'permissions' => $permissions,
        //         'access_token' => $token,
        //     ]
        // ];
        // return ['roles' => $roles, 'permissions' => $permissions, 'access_token' => $token];
        return Helpers::success(['roles' => $roles, 'permissions' => $permissions, 'access_token' => $token]);

        //return Helpers::success();

    }

    public function logout()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            throw new Exception('Token not provided');
        }

        JWTAuth::invalidate($token);

        return Helpers::success([], 'Successfully logged out.');

        //return ['message' => 'Successfully logged out'];
    }

}
