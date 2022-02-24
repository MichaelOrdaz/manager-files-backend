<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token,$subject,$typeMail,$data = [])
    {
        $this->token = $token;
        $this->subject = $subject;
        $this->typeMail = $typeMail;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mails = [
            'CONFIRM_MAIL_ASPIRANT' => 'mails.confirmMailAspirante',
            'RESET_PASSWORD' => 'mails.resetPassword',
            'FAIL_EXAM_ASPIRANT' => 'mails.failExamAspirant',
            'CONFIRM_MAIL_USUARIO' => 'mails.confirmMailUsuario',
            'ACEPT_MAIL_USUARIO' => 'mails.aceptMailAspirante',
            'PROGRESS_MAIL_USUARIO' => 'mails.progressMailAspirante',
        ];

        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->subject($this->subject)
            ->markdown($mails[$this->typeMail])->with([
            'token' => $this->token,
            'data' => $this->data,
        ]);
    }
}
