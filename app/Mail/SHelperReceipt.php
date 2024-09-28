<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SHelperReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($codeRequest,$details)
    {
        //
        $this->codeRequest = $codeRequest;
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $codeRequest =$this->codeRequest ;
        $details = $this->details ;
        return $this->view('code_request.receipt',compact('codeRequest', 'details'))
        
        ->subject('S Helper Receipt');
    }
}
