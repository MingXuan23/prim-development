<?php

namespace App\Mail;

use App\Models\Donation;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationReceipt extends Mailable
{
    use Queueable, SerializesModels;

    private $donation;
    private $transaction;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Donation $donation, Transaction $transaction)
    {
        $this->donation = $donation;
        $this->transaction = $transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.receipt');
    }
}
