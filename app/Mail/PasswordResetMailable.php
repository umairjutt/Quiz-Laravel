<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetMailable extends Mailable implements ShouldQueue
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
        $resetUrl = url("/password-reset?token={$this->token}&email={$this->email}");

        return $this->subject('Password Reset Request')
                    ->html("
                        <h1>Password Reset Request</h1>
                        <p>Click the link below to reset your password:</p>
                        <a href='{$resetUrl}'>{$resetUrl}</a>
                        <p>If you did not request a password reset, no further action is required.</p>
                        <p>Thanks,<br>" . config('app.name') . "</p>
                    ");
    }
}
