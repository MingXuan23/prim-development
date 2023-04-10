<?php

namespace App\Mail;

use App\Models\Donation;
use App\Models\Organization;
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
    private $organization;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Donation $donation, Transaction $transaction, Organization $organization)
    {
        $this->donation = $donation;
        $this->transaction = $transaction;
        $this->organization = $organization;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->donation->lhdn_reference_code != null)
        {
            return $this->view('mail.lhdn-receipt')
                    ->subject("Resit Derma " . $this->donation->nama)
                    ->with([
                        'organizationPic'    => $this->organization->organization_picture,
                        'organizationName'    => $this->organization->nama,
                        'organizationTelNo'   => $this->organization->telno,
                        'organizationEmail'   => $this->organization->email,
                        'ogranizationAddress' => $this->organization->address,
                        'ogranizationPostCode' => $this->organization->postcode,
                        'ogranizationCity' => $this->organization->city,
                        'ogranizationState' => $this->organization->state,
                        'transactionUsername' => $this->transaction->username,
                        'transactionIcno'    =>$this->transaction->icno,
                        'transactionUserAdress' => $this->transaction->address,
                        'transactionEmail'  => $this->transaction->email,
                        'transactionName'   => $this->transaction->description,
                        'transactionDate'   => $this->transaction->datetime_created,
                        'transactionAmount' => $this->transaction->amount,
                        'donationName'      => $this->donation->nama,
                        'doantionLHDNcode' => $this->donation->lhdn_reference_code,
                        'doantionStartDate' => $this->donation->date_started,
                        'doantionEndDate' => $this->donation->date_end,
                    ]);
        }

        return $this->view('mail.receipt')
                    ->subject("Resit Derma " . $this->donation->nama)
                    ->with([
                        'organizationName'  => $this->organization->nama,
                        'organizationTelNo'   => $this->organization->telno,
                        'organizationEmail'   => $this->organization->email,
                        'ogranizationAddress' => $this->organization->address,
                        'transactionUsername' => $this->transaction->username,
                        'transactionEmail'  => $this->transaction->email,
                        'transactionName'   => $this->transaction->description,
                        'transactionDate'   => $this->transaction->datetime_created,
                        'transactionAmount' => $this->transaction->amount,
                        'donationName'      => $this->donation->nama
                    ]);
    }
}
