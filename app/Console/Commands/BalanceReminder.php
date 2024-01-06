<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\NotifyBalance;
use App\Models\Booking;
use Illuminate\Support\Carbon;
class BalanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balances:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send reminder to the guests that have not pay for the balance of their booking';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentDate = Carbon::now();
        // get unpaid bookings
        $bookings = Booking::where('deposit_amount','!=', NULL)
        ->where([
            'transaction_balance_id' => NULL,
            'status' => 'Deposited',
        ])
        ->where(function ($query) use ($currentDate) {
            $query->whereDate('checkout', '=', $currentDate->addDay()->toDateString()) // Checkout is one day from now
                ->orWhereDate('checkout', '<=', $currentDate->toDateString()); // Checkout date has already passed
        })
        ->get();

        if(!$bookings->isEmpty()){
            foreach($bookings as $booking){
                $user = $booking->user;
                Mail::to($user->email)->send(new NotifyBalance($user,$booking));
            }
        }
        return 0;
    }
}
