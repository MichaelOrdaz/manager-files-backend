<?php

namespace App\Jobs;

use App\Mail\SendMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail as Email;
use Illuminate\Contracts\Queue\ShouldBeUnique;


/**
 * @author Enrique Sevilla <sevilla@puller.mx>
 * @version  1.0
 * Se crea un job para poder hacer uso de la cola de procesos
 * para ejecutar los jobs lo hace supervisor y manualmente
 *  php artisan queue:work
 */
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email,$token,$subject,$typeMail,$data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$token,$subject,$typeMail,$data = [])
    {
        $this->email = $email;
        $this->token = $token;
        $this->subject = $subject;
        $this->typeMail = $typeMail;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = new SendMail($this->token,$this->subject,$this->typeMail,$this->data);
        Email::to($this->email)->send($message);
    }
}
