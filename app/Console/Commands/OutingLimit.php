<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OutingLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outing:limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset outing limit for student';

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
        $result = DB::table('class_student')
        ->where('class_student.status', 1)
        ->update(['class_student.outing_limit' => NULL]);

        if($result){
            print_r('Success update outing limit');
        }else{
            print_r('Failed update outing limit');
        }

    }
}
