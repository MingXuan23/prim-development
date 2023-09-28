<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\NotifyFee;

class FeesReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To remind parents to pay their children fees before due date.';

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
        $studentDebt = DB::table('students as s')
            ->join('class_student as cs', 'cs.student_id', '=', 's.id')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('classes as c', 'c.id', '=', 'co.class_id')
            ->join('student_fees_new as sfn', 'sfn.class_student_id', '=', 'cs.id')
            ->join('fees_new as fn', 'fn.id', '=', 'sfn.fees_id')
            ->join('fees_recurring as fr', 'fr.student_fees_new_id', '=', 'sfn.id')
            ->join('organization_user_student as ous', 'ous.student_id', '=', 's.id')
            ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
            ->join('organizations as o', 'o.id', '=', 'ou.organization_id')
            ->join('users', 'users.id', '=', 'ou.user_id')
            ->select('s.*', 'sfn.*', 'users.*', 'cs.start_date as cs_startdate', 'fn.name as fn_nama', 'fn.start_date as fn_startdate', 'fn.end_date as fn_enddate', 'fr.finalAmount as fr_finalamount', 'o.nama as o_nama', 'c.nama as c_nama')
            ->where('sfn.status', 'Debt')
            ->where('fn.end_date', '<=', now())
            ->get();

        // dd($studentDebt);

        if ($studentDebt->count() > 0) {
            foreach ($studentDebt as $debt) {
                Mail::to($debt)->send(new NotifyFee($debt));
            }
        }

        return 0;   
    }
}
