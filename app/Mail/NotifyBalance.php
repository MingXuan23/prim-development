<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyBalance extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $booking;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$booking)
    {
        $this->user = $user;
        $this->booking = $booking;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Peringatan Pembayaran Baki')->view('emails.notifyBalance');
    }
}
