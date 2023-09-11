<?php

namespace App\Mail;

use App\User;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HomestayReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, Organization $organization, Transaction $transaction, User $user)
    {
        $this->$booking_order = DB::table('organiaztions as o')
        ->leftJoin('rooms as r', 'r.homestayid', 'o.id')
        ->leftJoin('bookings as b', 'b.roomid', 'r.roomid')
        ->where('b.bookingid', $booking->bookingid)
        ->orderBy('r.roomname')
        ->get();

        $this->booking = $booking;
        $this->organization = $organization;
        $this->transaction = $transaction;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $booking_order = $this->booking_order;
        $organization = $this->organization;
        $transaction = $this->transaction;
        $user = $this->user;

        return $this->view('homestay.receipt', compact('booking_order', 'organization', 'transaction', 'user'));
    }
}
