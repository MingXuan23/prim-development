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
    public function __construct(Room $room , Booking $booking, Organization $organization, Transaction $transaction, User $user)
    {
        $this->booking_order = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
        ->join('bookings','rooms.roomid','=','bookings.roomid')
        ->where('bookings.bookingid',$booking->bookingid) // Filter by the selected homestay
        ->select('organizations.id','organizations.nama','organizations.address', 'rooms.roomid', 'rooms.roomname', 'rooms.details', 'rooms.roompax', 'rooms.price', 'rooms.status','bookings.bookingid','bookings.checkin','bookings.checkout','bookings.totalprice','bookings.discount_received','bookings.increase_received','bookings.booked_rooms','bookings.deposit_amount','bookings.status')
        ->get();

        $this->booking = $booking;
        $this->organization = $organization;
        $this->transaction = $transaction;
        $this->user = $user;
        $this->room = $room;
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
        $room = $this->room;
        return $this->view('homestay.receipt', compact('room','booking_order', 'organization', 'transaction', 'user'));
    }
}
