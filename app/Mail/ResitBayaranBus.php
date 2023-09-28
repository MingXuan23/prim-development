<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Model\Organization;
use App\Models\Bus;
use App\Models\Bus_Booking;
use App\Models\NotifyBus;
use Illuminate\Queue\SerializesModels;

class ResitBayaranBus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Bus_Booking $booking, User $user)
    {
        $this->bus_booking = Organization::join('buses', 'organizations.id', '=', 'buses.id_organizations')
        ->join('bus_bookings','buses.id','=','bus_bookings.id_bus')
        ->where('bus_bookings.id',$booking->id) 
        ->select('bus_bookings.id as bookid', 'buses.bus_registration_number', 'buses.booked_seat', 'buses.available_seat', 'buses.trip_number', 'buses.bus_depart_from', 'buses.bus_destination', 'buses.departure_time', 'buses.departure_date', 'buses.price_per_seat')
        ->get();

        $this->booking = $booking;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $bus_booking = $this->bus_booking;
        $booking = $this->booking;
        $user = $this->user;

        return $this->view('bus.resitbayaran', compact('bus_booking', 'booking', 'user'));
    }
}
