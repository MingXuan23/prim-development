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
        $this->grab_notify = Organization::join('buses', 'organizations.id', '=', 'buses.id_organizations')
        ->join('bus_notifys','buses.id','=','bus_notifys.id_bus')
        ->where('bus_notifys.id',$notify->id) 
        ->select('buses.pick_up_point','buses.destination_name','buses.available_time', 'buses.car_brand', 'buses.car_name', 'buses.car_registration_num')
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
        $grab_notify = $this->grab_notify;
        $notify = $this->notify;
        $user = $this->user;

        return $this->view('grab.notifyemail', compact('grab_notify', 'notify', 'user'));
    }
}
