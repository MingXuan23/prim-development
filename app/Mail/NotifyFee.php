<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyFee extends Mailable
{
    use Queueable, SerializesModels;

    public $debt;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($debt)
    {
        $this->debt = $debt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Peringatan ' . $this->debt->fn_nama . ' Perlu Dibayar Sebelum ' . date('d-m-Y', strtotime($this->debt->fn_enddate)))->view('emails.notifyFeeMail');
    }
}
