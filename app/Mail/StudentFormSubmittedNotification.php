<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentFormSubmittedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $student; // Make the student information accessible in the email

    /**
     * Create a new message instance.
     *
     * @param $student
     */
    public function __construct($student)
    {
        $this->student = $student; // Assign the student information
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Form Submission is Received')
                    ->view('emails.student_form_submitted') // Link to the email view
                    ->with([
                        'name' => $this->student->name, // Pass the student's name to the view
                        'email' => $this->student->email,
                    ]);
    }
}
