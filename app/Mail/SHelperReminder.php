<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SHelperReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pending_request)
    {
        //
        $this->pending_request = $pending_request;
        //$this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pending_request = $this->pending_request;
        return $this->view('code_request.helper_reminder', compact('pending_request'))
        ->subject('S Helper Reminder');
    }
}
