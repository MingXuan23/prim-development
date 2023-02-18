<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\contract\Mailer;
use Illuminate\Support\Facades\Mail;

use Illuminate\Queue\SerializesModels;

class NotifyArrive extends Mailable
{
    use Queueable, SerializesModels;
    private $student;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student)
    {
        //
        $this->student = $student;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $student = $this->student;
        return $this->subject('Notifikasi Pelajar Sampai Destinasi')
            ->view('emails.arriveMail', compact('student'));
    }
}
