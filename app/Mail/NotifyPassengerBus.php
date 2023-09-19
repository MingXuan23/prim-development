<?php

namespace App\Mail;

use App\User;
use App\Models\Organization;
use App\Models\Bus_Booking;
use App\Models\Bus;
use App\Models\NotifyBus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyPassengerBus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(NotifyBus $notify,User $user)
    {
        $this->bus_notify = Organization::join('buses', 'organizations.id', '=', 'buses.id_organizations')
        ->join('bus_notifys','buses.id','=','bus_notifys.id_bus')
        ->where('bus_notifys.id',$notify->id) 
        ->select('buses.bus_depart_from','buses.bus_destination','bus_notifys.time_notify','buses.departure_time', 'buses.trip_number', 'buses.bus_registration_number', 'buses.departure_date', 'buses.price_per_seat')
        ->get();

        $this->notify = $notify;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $bus_notify = $this->bus_notify;
        $notify = $this->notify;
        $user = $this->user;

        return $this->view('bus.notifyemail', compact('bus_notify', 'notify', 'user'));
    }
}
