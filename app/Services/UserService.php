<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ManagerAddedMailable;
use App\Mail\StudentApprovedMailable;
use App\Mail\StudentRejectedMailable;
use Illuminate\Pipeline\Pipeline;


class UserService
{
    public function createManager($data)
    {
        // Create manager with a random password
        $manager = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(str()->random(10)), // Temporary random password
        ]);

        // Assign the Manager role
        $manager->assignRole('Manager');

        // Generate password reset token
        $token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($manager);

        // Send notification to the manager
        Mail::to($manager->email)->send(new ManagerAddedMailable($token, $manager->email));

        // Log the creation activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($manager)
            ->withProperties(['email' => $manager->email])
            ->log('Created a new Manager');

        return ['message' => 'Manager created and notification sent.'];
    }

    public function listStudents()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'Student');
        })->get();
    }

    public function approveStudent($studentId)
    {
        $student = User::findOrFail($studentId);

        // Update status and send mail
        $student->update(['status' => 'accepted']);
        $student->assignRole('Student');

        $token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($student);
        Mail::to($student->email)->send(new StudentApprovedMailable($token, $student->email));

        return ['message' => 'Student approved, status updated, and email sent.'];
    }

    public function rejectStudent($studentId)
    {
        $student = User::findOrFail($studentId);

        // Update the student's status to 'rejected'
        $student->update(['status' => 'rejected']);

        // Optionally remove the 'Student' role if assigned earlier
        $student->removeRole('Student');

        // Send rejection email
        Mail::to($student->email)->send(new StudentRejectedMailable($student));

        return ['message' => 'Student rejected, status updated to rejected, and notification sent.'];
    }

    public function filterStudents($status = null)
    {
        return app(Pipeline::class)
            ->send(User::query()->whereHas('roles', function ($query) {
                $query->where('name', 'Student');
            }))
            ->through([
                function ($query) use ($status) {
                    if ($status) {
                        if ($status === 'approved') {
                            $query->where('status', 'accepted');
                        } elseif ($status === 'rejected') {
                            $query->where('status', 'rejected');
                        } elseif ($status === 'pending') {
                            $query->where('status', 'pending');
                        }
                    }
                    return $query;
                }
            ])
            ->thenReturn()
            ->get();
    }
}
