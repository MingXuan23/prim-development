<?php

namespace App\Mail;

use App\User;
use App\Models\Organization;
use App\Models\Grab_Booking;
use App\Models\Grab_Student;
use App\Models\NotifyGrab;
use App\Models\Destination_Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResitBayaranGrab extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Grab_Booking $booking, User $user)
    {
        $this->grab_booking = Organization::join('grab_students', 'organizations.id', '=', 'grab_students.id_organizations')
        ->join('destination_offers','grab_students.id','=','destination_offers.id_grab_student')
        ->join('grab_bookings','destination_offers.id','=','grab_bookings.id_destination_offer')
        ->where('grab_bookings.id',$booking->id) 
        ->select('destination_offers.pick_up_point','destination_offers.destination_name','destination_offers.available_time', 'grab_students.car_brand', 'grab_students.car_name', 'grab_students.car_registration_num', 'grab_students.number_of_seat')
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
        $grab_booking = $this->grab_booking;
        $booking = $this->booking;
        $user = $this->user;

        return $this->view('grab.resitbayaran', compact('grab_booking', 'booking', 'user'));
    }
}
