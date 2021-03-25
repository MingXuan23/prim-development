<?php

namespace App\Console\Commands;

use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DonationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donation:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check update status donation if expired';

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
        $currentdate = Carbon::now()->format('Y-m-d');
        $expiredate  = Donation::where('date_end', $currentdate)->update(array('status' => 0));
        if($expiredate){
            print_r('Success update status donation');
        }else{
            print_r('Failed update status donation');
        }
    }
}
