<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentApprovedMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param string $token
     * @param string $email
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $resetLink = url('/password-reset?token=' . $this->token . '&email=' . urlencode($this->email));

        return $this->subject('Student Approval Confirmation')
                    ->html("
                        <p>Hello,</p>
                        <p>Your student registration has been approved!</p>
                        <p>Please click the following link to set your password:</p>
                        <p><a href='{$resetLink}'>Set Your Password</a></p>
                        <p>Or copy and paste this link into your browser: {$resetLink}</p>
                        <p>Thank you,</p>
                        <p>Your team.</p>
                    ");
    }   
}
