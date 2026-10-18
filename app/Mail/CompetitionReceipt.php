<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompetitionReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $get_participant_to_organization;
    public $get_transaction;
    public $transaction;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($get_participant_to_organization, $get_transaction, $transaction)
    {
        $this->get_participant_to_organization = $get_participant_to_organization;
        $this->get_transaction = $get_transaction;
        $this->transaction = $transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Resit Pembayaran Pendaftaran Pertandingan')->view('competition.receipt');
    }
}
