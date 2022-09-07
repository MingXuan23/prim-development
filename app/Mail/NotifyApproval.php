<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\contract\Mailer;
use Illuminate\Support\Facades\Mail;

use Illuminate\Queue\SerializesModels;

class NotifyApproval extends Mailable
{
    use Queueable, SerializesModels;
    protected $student;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student, $status)
    {
        //
        $this->student = $student;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject('Permohonan Pelajar Keluar Sekolah')
            ->view('emails.approvalMail')
            ->with([
                'student'    =>  $this->student,
                'approvalStatus'    => $this->status
            ]);
    }
}
