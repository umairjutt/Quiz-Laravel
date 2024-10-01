<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentRejectedMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $student;

    /**
     * Create a new message instance.
     *
     * @param object $student
     */
    public function __construct($student)
    {
        $this->student = $student;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Student Application Has Been Rejected')
                    ->html("
                        <p>Hello {$this->student->name},</p>
                        <p>We regret to inform you that your student application has been rejected.</p>
                        <p>If you have any questions, feel free to contact us.</p>
                        <p>Thank you,</p>
                        <p>Your Team</p>
                    ");
    }
}
