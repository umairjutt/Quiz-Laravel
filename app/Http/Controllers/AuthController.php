<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterStudentRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Password;


class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registerStudent(RegisterStudentRequest $request)
    {
        try {
            $response = $this->authService->registerStudent($request->all());
            return response()->json($response, 201);
        } catch (\Exception $e) {
            \App\Helpers\Helpers::logError($e); // Log error to DB and file
            return response()->json(['error' => 'Error registering student.'], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $response = $this->authService->login($request->only('email', 'password'));
            return $response;
        } catch (\Exception $e) {
            \App\Helpers\Helpers::logError($e); // Log error to DB and file
            return response()->json(['error' => 'Error during login.'], 400);
        }
    }

    public function logout(Request $request)
    {
            $response = $this->authService->logout();
            return $response;
        
    }

    // Password Reset Request
    public function passwordReset(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

        // Send the password reset link using Mailable
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => 'Password reset link sent.'])
                : response()->json(['message' => 'Unable to send password reset link.'], 500);

        } catch (\Exception $e) {
            \App\Helpers\Helpers::logError($e); // Log error to DB and file
            return response()->json(['message' => 'An error occurred while sending password reset link.'], 500);
        }
    }


    // Password Reset Confirmation
    public function passwordResetConfirm(Request $request)
    {
    try {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => \Hash::make($password),
                ])->save();

                // Optional: Revoke all tokens
                $user->tokens()->delete();

                // Invalidate JWT token
                \JWTAuth::factory()->invalidate(\JWTAuth::getToken());
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successful.'])
            : response()->json(['message' => 'Password reset failed.'], 500);

    } catch (\Exception $e) {
        \App\Helpers\Helpers::logError($e); // Log error to DB and file
        return response()->json(['message' => 'An error occurred during password reset.'], 500);
    }
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

}
