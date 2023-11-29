<?php

namespace App\Http\Controllers\Schedule;
use stdClass;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;
use Illuminate\Notifications\Notification;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\ServiceAccount;


use App\User;



class ScheduleApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     protected $notification;

    public function __construct()
    {
        //$this->notification = Firebase::messaging();
    }


     public function login(Request $request)
     {  
        $credentials = $request->only('email', 'password');
        $phone = $request->get('email');
        //return response()->json(['user',$credentials],200);
        if(is_numeric($request->get('email'))){
            $user = User::where('icno', $phone)->first();
           
            if ($user) {
                //dd($user);
                //return ['icno' => $phone, 'password' => $request->get('password')];
                $credentials = ['icno'=>$phone, 'password' => $request->get('password')];
            }
            else{
                if(!$this->startsWith((string)$request->get('email'),"+60") && !$this->startsWith((string)$request->get('email'),"60")){
                    if(strlen((string)$request->get('email')) == 10)
                    {
                        $phone = str_pad($request->get('email'), 12, "+60", STR_PAD_LEFT);
                    } 
                    elseif(strlen((string)$request->get('email')) == 11)
                    {
                        $phone = str_pad($request->get('email'), 13, "+60", STR_PAD_LEFT);
                    }   
                } else if($this->startsWith((string)$request->get('email'),"60")){
                    if(strlen((string)$request->get('email')) == 11)
                    {
                        $phone = str_pad($request->get('email'), 12, "+", STR_PAD_LEFT);
                    } 
                    elseif(strlen((string)$request->get('email')) == 12)
                    {
                        $phone = str_pad($request->get('email'), 13, "+", STR_PAD_LEFT);
                    }   
                }
                $credentials = ['telno'=>$phone,'password'=>$request->get('password')];
            }
        }
        else if(strpos($request->get('email'), "@") !== false){
            $credentials = ['email'=>$phone,'password'=>$request->get('password')];
        }
        else{
            $credentials =['telno' => $phone, 'password'=>$request->get('password')];

        }


        if (Auth::attempt($credentials)) {
            $user = Auth::User();
            if($request->device_token){
                $user->device_token =$request->device_token;
                $user->save();
            }
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'device_token'=>$user->device_token
                

            ], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
         
     }

     public function getSlotTime($schedule,$day,$slot){
        $time_off = json_decode($schedule->time_off,true);

        $timeOffSlot = array_filter($time_off, function ($breakSlot) use ($slot,$day) {
                
            return $slot > $breakSlot['slot'] && isset($breakSlot['duration']) && (!isset($breakSlot['day'])||in_array($day, $breakSlot['day']));
        });

        //dd($timeOffSlot);
        $break_to_add=0;
        foreach($timeOffSlot as $breakSlot){
            $break_to_add = $breakSlot['duration'] -$schedule->time_of_slot;
        }

        $minutes_to_add = $schedule->time_of_slot * ($slot-1) + $break_to_add; // Adjust this value to the number of minutes you want to add
        $time = \DateTime::createFromFormat('H:i:s',  $schedule->start_time);

        // Add minutes to the DateTime instance
        $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
        $result_time = $time->format('H:i:s');

        $filteredTimeOff = collect($time_off)->first(function ($breakSlot) use ($day,$slot) {
            return $breakSlot['slot'] == $slot && in_array($day, $breakSlot['day'] ?? []) && isset($breakSlot['duration']);
        });
        $duration = $schedule->time_of_slot;

        if($filteredTimeOff)
            $duration=$filteredTimeOff['duration'];

        return ['time'=> $result_time,'duration'=>$duration];
       
     }

     public function getSchedule($id){

        //$user = User::find($request->userId);

        $user = User::find($id);
        if($user ==null){
            return response()->json(['error' => 'This user did not exist'], 401);
        }
        $school =DB::table('organizations as o')
            ->join('organization_user as ou','ou.organization_id','o.id')
            ->where('ou.role_id',5)
            ->where ('ou.user_id',$user->id)
            ->select('o.*')
            ->first();

        if($school){
            $schedule = DB::table('schedules as s')
            ->leftJoin ('schedule_version as sv','sv.schedule_id','s.id')
            ->leftJoin('schedule_subject as ss','ss.schedule_version_id','sv.id')
            ->leftJoin('classes as c','c.id','ss.class_id')
            ->leftJoin('subject as sub','sub.id','ss.subject_id')
            ->where('s.organization_id',$school->id)
            ->where('ss.teacher_in_charge',$user->id)
            ->where('sv.status',1)
            ->select('ss.id','c.nama as class','sub.name as subject','s.start_time','s.time_of_slot','ss.slot','s.time_off','ss.day')
            ->get();

            
            foreach($schedule as $s){
                //if(isset($s->duration)){
                    $time_info= $this->getSlotTime($s,$s->day,$s->slot);
                    $s->time=$time_info['time'];
                    $s->duration=$time_info['duration'];
                    $s->category="Normal";
                   
                    unset($s->time_off);
                    unset($s->start_time);
            }

            $relief_schedule = DB::table('leave_relief as lr')
            ->leftJoin('schedule_subject as ss','ss.id','lr.schedule_subject_id')
            ->leftJoin ('schedule_version as sv','sv.schedule_id','ss.schedule_version_id')
            ->leftJoin('schedules as s','s.id','sv.schedule_id')
            ->leftJoin('classes as c','c.id','ss.class_id')
            ->leftJoin('subject as sub','sub.id','ss.subject_id')
            ->leftJoin('users as u','u.id','ss.teacher_in_charge')
            ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
            ->where('s.organization_id',$school->id)
            ->where('lr.replace_teacher_id',$user->id)
            ->where('lr.confirmation','Confirmed')
            ->whereBetween('tl.date', [Carbon::now()->addDays(-7)->format('Y-m-d'), Carbon::now()->addDays(21)->format('Y-m-d')])
            ->where('sv.status',1)
            ->where('tl.status',1)
            ->select('ss.id','c.nama as class','sub.name as subject','s.start_time','s.time_of_slot','ss.slot','s.time_off','ss.day','u.name as relatedTeacher','tl.date')
            ->get();
            //dd($schedule);
            $onLeave_schedule = DB::table('leave_relief as lr')
            ->leftJoin('schedule_subject as ss','ss.id','lr.schedule_subject_id')
            ->leftJoin ('schedule_version as sv','sv.schedule_id','ss.schedule_version_id')
            ->leftJoin('schedules as s','s.id','sv.schedule_id')
            ->leftJoin('classes as c','c.id','ss.class_id')
            ->leftJoin('subject as sub','sub.id','ss.subject_id')
            ->leftJoin('users as u','u.id','lr.replace_teacher_id')
            ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
            ->where('s.organization_id',$school->id)
            ->where('tl.teacher_id',$user->id)
            ->whereBetween('tl.date', [Carbon::now()->addDays(-7)->format('Y-m-d'), Carbon::now()->addDays(21)->format('Y-m-d')])
            ->where('sv.status',1)
            ->where('tl.status',1)
            ->select('ss.id','c.nama as class','sub.name as subject','s.start_time','s.time_of_slot','ss.slot','s.time_off','ss.day','u.name as relatedTeacher','tl.date','lr.confirmation')
            ->get();

            //dd($relief_schedule);
            foreach($relief_schedule as $r){
                //if(isset($s->duration)){

                    $r->category ="Relief";
                    $time_info= $this->getSlotTime($r,$r->day,$r->slot);
                    $r->time=$time_info['time'];
                    $r->duration=$time_info['duration'];
                   
                   
                    unset($r->time_off);
                    unset($r->start_time);
            }
            
            foreach($onLeave_schedule as $r){
                //if(isset($s->duration)){

                    $r->category ="Leave";
                    $time_info= $this->getSlotTime($r,$r->day,$r->slot);
                    $r->time=$time_info['time'];
                    $r->duration=$time_info['duration'];
                    if($r->confirmation !="Confirmed")
                        $r->relatedTeacher ="No Teacher";

                    unset($r->confirmation);
                   
                    unset($r->time_off);
                    unset($r->start_time);
            }
            return response()->json(['schedule'=>$schedule,'leave'=>$onLeave_schedule,'relief'=>$relief_schedule]);

           


        }
        
        return response()->json(['error' => 'Invalid data provided'], 401);


        //return response()->json(["error"=>"This user are not any teacher in any school"]);
     }

     public function getTeacherInfo($id){

        $user = User::find($id);
        if($user ==null){
            return response()->json(["error"=>"This user did not exist"]);
        }
        $school =$this->getSchools($user->id)->first();
        
        if($school){
            $isAdmin = $this->checkAdmin($user->id,$school->id);
            $allcount=0;
            $pendingReliefCount = DB::table('leave_relief as lr')
                ->join('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                ->where('lr.replace_teacher_id',$user->id)
                ->where('lr.confirmation',"Pending")
                ->where('tl.status',1)
                ->where('tl.date','>=',Carbon::today())
                ->count();

            if($isAdmin){
                $allcount = DB::table('organizations as o')
                    ->leftJoin('schedules as s','s.organization_id','o.id')
                    ->leftJoin('schedule_version as sv','sv.schedule_id','s.id')
                    ->leftJoin('schedule_subject as ss','ss.schedule_version_id','sv.id')
                    ->leftJoin('leave_relief as lr','lr.schedule_subject_id','ss.id')
                    ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                    ->where('lr.confirmation','!=','Confirmed')
                    ->where('o.id',$school->id)
                    ->where('sv.status',1)
                    ->where('s.status',1)
                    ->where('tl.status',1)
                    ->where('tl.date','>=',Carbon::today())
                    ->select('lr.id')  // Select the id column to make unique method work
                    ->distinct()       // Use distinct instead of unique
                    ->count();
            }
            return response()->json(['school_name'=>$school->nama,'school_id'=>$school->id,'pendingReliefCount'=>$pendingReliefCount,'isAdmin'=>$isAdmin,'allCount'=>$allcount]);
        }
        return response()->json(['school_name'=>'No related school','school_id'=>-1]);

     }


     public function getLeaveType(){
        $type=DB::table('leave_type')
            ->where('status',1)
            ->get();
        return response()->json(['type'=>$type]);
     }

     public function submitLeave(Request $request){

        try{

            $period = new stdClass();
            $date = Carbon::createFromDate($request->date);
            
            if($request->isLeaveFullDay){
                $period->fullday=true;
            }else{
                $period->fullday=false;
                $period->start_time= $request->start_time;
                $period->end_time=$request->end_time;

            }

            $period = json_encode($period);
            $user = User::find($request->teacher_id);

            if(! DB::table('leave_type')->where('id',$request->leave_type)->exists()){
                return response()->json(['error' => 'Leave Type value error'], 401);
            }

            if($user){
            
            $existConflict =DB::table('teacher_leave')
                    ->where('date',$date)
                    ->where('status',1)
                    ->where('teacher_id',$user->id)
                    ->where(function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            $query->where('period->end_time', '>', $request->start_time)
                                ->where('period->start_time', '<', $request->end_time);
                        })->orWhere('period->fullday', true);
                    })
                    ->exists();

            if($existConflict){
                 return response()->json(['error' => 'The selected time is conflict with the record before'], 401);
            }
            
            
            $leave_id =  DB::table('teacher_leave')->insertGetId([
                
                    'period'=>$period,
                    'date'=>$date,
                    'desc'=>  $request->desc,
                    'status'=>1,
                    'teacher_id'=>$user->id
        
                ]);

                
                $classRelated = DB::table('schedule_subject as ss')
                ->join('schedule_version as sv','sv.id','ss.schedule_version_id')
                ->join('schedules as s','s.id','sv.schedule_id')
                ->where('ss.day',$date->dayOfWeek)
                ->where('ss.teacher_in_charge',$user->id)
                ->where('s.status',1)
                ->where('sv.status',1)
                ->select('s.*','ss.id as schedule_subject_id','ss.day as day','ss.slot as slot')
                ->get();
                
                foreach($classRelated as $c){
                    if($request->isLeaveFullDay){
                        $insert = DB::table('leave_relief')->insert([
                            'teacher_leave_id'=>$leave_id,
                            'schedule_subject_id'=>$c->schedule_subject_id
                        ]);
                    }else{
                        $start = Carbon::createFromFormat('H:i:s', $request->start_time);
                        $end = Carbon::createFromFormat('H:i:s', $request->end_time);
                        $time_info=$this->getSlotTime($c,$c->day,$c->slot);
                        $check = Carbon::createFromFormat('H:i:s', $time_info['time'] );

                        
                        // check if the time is between start and end
                        if ($check->between($start, $end) || $check->addMinutes($time_info['duration']-1)->between($start,$end)) {
                            $insert = DB::table('leave_relief')->insert([
                                'teacher_leave_id'=>$leave_id,
                                'schedule_subject_id'=>$c->schedule_subject_id
                            ]);
                        } 
                    }
                }
                $count = DB::table('leave_relief')->where('teacher_leave_id',$leave_id)->count();
                return response()->json(['Success'=>'Leave Submit Sucessfully.Total '.$count.' classes affected.']);
                
            }
            return response()->json(['error' => 'This user did not exist'], 401);

        }catch (Exception $e) {
            return response()->json(['error' => 'Server Error']);

        }
     }

     //make sure the school id is validate before
     public function checkAdmin($userId,$schoolId){
        return DB::table('organizations as o')
                ->join('organization_user as ou','ou.organization_id','o.id')
                ->whereIn('ou.role_id',[2,4])
                ->where('o.id',$schoolId)
                ->where('ou.status',1)
                ->where('ou.user_id',$userId)
                ->exists();
     }

     public function getSchools($user_id){
        return DB::table('organizations as o')
            ->join('organization_user as ou','ou.organization_id','o.id')
            ->whereIn('ou.role_id',[2,4,5])
            ->where ('ou.user_id',$user_id)
            ->whereIn('o.type_org',[1,2,3])
            ->where('ou.status',1)
            ->select('o.*')
            ->get();
     }


     public function sendNotification($id)
     {  $user =User::find($id);

       // dd($user);
        if($user->device_token){

            $device_token =[];
            $url = 'https://fcm.googleapis.com/fcm/send';
            array_push($device_token,$user->device_token);
        $serverKey = getenv('FCM_SERVER_KEY');
        //$serverKey = getenv('PRODUCTION_BE_URL');
        
       
        $data = [
            "registration_ids" => $device_token,
            "notification" => [
                "title" => "test",
                "body" =>"hello",
            ]
        ];

        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key='. $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: '. curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        // FCM response
        dd($result);

            return response()->json(["success"]);
        }
        return response()->json(["failed"]);
     }


    public function getPendingRelief(Request $request){
        $user = User::find($request->user_id);

        if($user ==null){
            return response()->json(["error"=>"This user did not exist"]);
        }
        $school =$this->getSchools($user->id)->first();
        //return response()->json(["error"=>$school]);
        if($school){
            $pendingRelief = DB::table('leave_relief as lr')
            ->leftJoin('schedule_subject as ss','ss.id','lr.schedule_subject_id')
            ->leftJoin ('schedule_version as sv','sv.schedule_id','ss.schedule_version_id')
            ->leftJoin('schedules as s','s.id','sv.schedule_id')
            ->leftJoin('classes as c','c.id','ss.class_id')
            ->leftJoin('subject as sub','sub.id','ss.subject_id')
            ->leftJoin('users as u','u.id','ss.teacher_in_charge')
            ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
            ->where('s.organization_id',$school->id)
            ->where('lr.replace_teacher_id',$user->id)
            ->where('lr.confirmation',"Pending")
            ->where('tl.date','>=',Carbon::today())
            ->where('sv.status',1)
            ->where('tl.status',1)
            ->select('ss.id','c.nama as class','sub.name as subject','s.start_time','s.time_of_slot','ss.slot','s.time_off','ss.day','u.name as relatedTeacher','tl.date')
            ->get();


                foreach($pendingRelief as $r){
                    //if(isset($s->duration)){
    
                        $r->category ="PendingRelief";
                        $time_info= $this->getSlotTime($r,$r->day,$r->slot);
                        $r->time=$time_info['time'];
                        $r->duration=$time_info['duration'];
                       
                       
                        unset($r->time_off);
                        unset($r->start_time);
                }

            $allPending=false;
            if($request->isAdmin&&$this->checkAdmin($user->id,$school->id)){
                $allPending = DB::table('organizations as o')
                ->join('schedules as s','s.organization_id','o.id')
                ->join('schedule_version as sv','sv.schedule_id','s.id')
                ->join('schedule_subject as ss','ss.schedule_version_id','sv.id')
                ->leftJoin('leave_relief as lr','lr.schedule_subject_id','ss.id')
                ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                ->leftJoin('subject as sub','sub.id','ss.subject_id')
                ->leftJoin('users as u','u.id','ss.teacher_in_charge')
                ->leftJoin('users as ur','ur.id','lr.replace_teacher_id')
                ->leftJoin('classes as c','c.id','ss.class_id')
                ->where('lr.confirmation','<>',"Confirmed")
                ->where('sv.status',1)
                ->where('s.status',1)
                ->where('tl.status',1)
                ->where('tl.date','>=',Carbon::today())
                ->select('lr.id as leave_relief_id','ss.id as schedule_subject_id','c.nama as class','sub.name as subject',
                's.start_time','s.time_of_slot','ss.slot','s.time_off','ss.day','u.name as leaveTeacher','ur.name as reliefTeacher','tl.date')
                ->get();

                foreach($allPending as $a){
                    //if(isset($s->duration)){
                        $time_info= $this->getSlotTime($a,$a->day,$a->slot);
                        $a->time=$time_info['time'];
                        $a->duration=$time_info['duration'];
             
                        unset($a->time_off);
                        unset($a->start_time);
                }
            }
            return response()->json(['pendingRelief'=>$pendingRelief,'allPending'=>$allPending]);
        }
        return response()->json(["error"=>"You have not any school"]);

    }

    public function submitReliefResponse(Request $request){
        $user = User::find($request->user_id);

        $update =DB::table('leave_relief')
                ->where('id',$request->leave_relief_id)
                ->where('replace_teacher_id',$user->id)
                ->update([
                    'Confirmation'=>$request->response,
                    'desc'=>$request->desc
                ]);

        return response()->json(['result',$update]);
    }

    public function getHistory($user_id)
    {
        $school = $this->getSchools($user_id)->first();
        $isAdmin =$this->checkAdmin($user_id,$school->id);

        $report = new stdClass();

        $report->leaveCount = DB::table('teacher_leave as tl')
                ->where('tl.teacher_id',$user_id)
                ->count();

        $report->reliefCount = DB::table('leave_relief')
            ->where('replace_teacher_id',$user_id)
            ->count();
        if($isAdmin){
            $adminReport =new stdClass();

            $adminReport->totalReliefCount = DB::table('teacher_leave as tl')
            ->join('organization_user as ou','ou.user_id','tl.teacher_id')
            ->where('ou.organization_id',$school->id)
            ->count(); 

            $adminReport->totalLeavefCount = DB::table('leave_relief as lr')
            ->join('organization_user as ou','ou.user_id','lr.replace_teacher_id')
            ->where('ou.organization_id',$school->id)
            ->count(); 

            return response()->json(['report'=>$report,'report'=>$adminReport]);
        }
        
        return response()->json(['report'=>$report]);
    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
