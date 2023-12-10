<?php

namespace App\Http\Controllers\Schedule;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\Organization;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Imports\ScheduleImport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;
use App\User;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization = $this->getOrganizationByUserId();
        $classes = $this->getClassByUserId();
        $schedule = $this->getScheduleId();
        return view('schedule.index', compact('organization', 'classes', 'schedule'));
    }

    public function manageReliefIndex()
    {
        $organization = $this->getOrganizationByUserId();
        $classes = $this->getClassByUserId();
        // $schedule = $this->getScheduleId();
        return view('manage_relief.index', compact('organization', 'classes'));
    }

    public function reliefReportIndex()
    {
        $organization = $this->getOrganizationByUserId();
        $classes = $this->getClassByUserId();
        // $schedule = $this->getScheduleId();
        return view('relief_report.index', compact('organization', 'classes'));
    }

    //using ajax to call and get the pending relief
    public function getPendingRelief(Request $request){
        $oid = $request->organization;
        $teachers='';
        $date =$request->date;
        // dd($request);
        $relief =$this->getAllRelief($oid, $date);
        // dd($relief);
       // $teachers = $this->getFreeTeacher($request); // New method to get available teachers
        return response()->json(['pending_relief' => $relief]);

        //return response()->json(['pending_relief' => $relief, 'available_teachers' => $teachers]);
    }

    // get all pending, null and rejected relief
    public function getAllRelief($oid, $date){
        $organization = $this->getOrganizationByUserId();
        
        if($organization->contains('id', $oid)){
            $relief = DB::table('leave_relief as lr')
            ->leftJoin('schedule_subject as ss','ss.id','lr.schedule_subject_id')
            ->leftJoin('classes as c','c.id','class_id')
            ->leftJoin('subject as sub','sub.id','ss.subject_id','sub.id')
            ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
            ->leftJoin('schedules as s','s.id','sv.schedule_id')
            ->leftJoin('users as u1','u1.id','ss.teacher_in_charge')
            ->leftJoin('users as u2','lr.replace_teacher_id','u2.id')
            ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
            ->where(function ($query) {
                $query->where('lr.confirmation', 'Rejected')
                    ->orWhereNull('lr.confirmation');
            })
            ->where('lr.status',1)
            ->where('s.organization_id',$oid)
            ->where('sv.status',1)
            ->where('tl.date',$date)
            ->orderBy('lr.confirmation')
            ->select('lr.id as leave_relief_id','lr.confirmation','ss.id as schedule_subject_id','tl.date','tl.desc'
            ,'sub.name as subject','u1.name as leave_teacher','u2.name as relief_teacher','ss.slot','ss.day','s.time_of_slot','s.start_time','s.time_off','c.nama as class_name')
            ->get();

            foreach($relief as $r){
                $result=$this->getSlotTime($r,$r->day,$r->slot);
                $r->time = $result['time'];
                $r->duration =$result['duration'];
                unset($r->time_of_slot,$r->start_time,$r->time_off);
            }
            return $relief;
        }
    }

    //to get the teacher burden information
    public function getTeacherInfo($teacher_id,$organization_id,$schedule_subject,$date){
        $teacher =new stdClass();
        $teacher->id =$teacher_id;
        $teacher->name = User::find($teacher_id)->name;
        $teacher->normal_class = DB::table('schedule_subject as ss' )
                        ->leftJoin('schedule_version as sv','sv.id','schedule_version_id')
                        ->where('sv.status',1)
                        ->where('ss.teacher_in_charge',$teacher_id)
                        ->count('ss.id');

        $date = '2023-12-06';

        // Split the date string into year, month, and day
        list($year, $month, $day) = explode('-', $date);
        
        // Create a Carbon instance
        $now = Carbon::createFromDate($year, $month, $day);
        
        $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i');

        //dd($weekStartDate,$weekEndDate,$now);
        $teacher->relief_class = DB::table('leave_relief as lr')
                                ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                                ->where('lr.status',1)
                                ->where('lr.replace_teacher_id',$teacher_id)
                                ->whereBetween('tl.date',[$weekStartDate,$weekEndDate])
                                ->count('lr.id');
        
        $teacher->leave_class =DB::table('leave_relief as lr')
                    ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                    ->where('lr.status',1)
                    ->where('tl.teacher_id',$teacher_id)
                    ->whereBetween('tl.date',[$weekStartDate,$weekEndDate])
                    ->count('lr.id');
       

        return $teacher;

    }

    //to auto suggesstion
    public function autoSuggestRelief(Request $request){
        
        $request->date = Carbon::now();
        $pendingRelief = $request -> pendingRelief;
        $date = $request->date;
        $organization =$request->organization;
        $criteria = $request->criteria;

        $relief_draft =[];
        foreach($pendingRelief as $p){
            $data =explode('-', $p);
            $leave_relief_id =$data[0];
            $schedule_subject = DB::table('schedule_subject')->where('id',$data[1])->first();
            $teacherList = $this->getAvailableTeacherList($schedule_subject,$date,$organization);
            
            // criteria
            foreach($teacherList as $t){
                $draft = new stdClass();
                $draft->teacher_id = $t->id;
                $draft->teacher_name =$t->name;

                $draft->schedule_subject_id=$schedule_subject->id;
                $draft->leave_relief_id = $leave_relief_id;
                array_push($relief_draft, $draft);
                break;
            }

        }

        return response()->json(['relief_draft'=>$relief_draft]);
        
       
    }

    //get all teacher available for the moment
    public function getAvailableTeacherList($schedule_subject,$date,$organization){
        $teachers = DB::table('organization_user as ou')
        ->where('ou.organization_id',$organization)
        ->where('ou.role_id',5)
        ->select('ou.user_id')
        ->get()
        ->toArray();

       
        $teacher_info =[];
        //dd($teachers);
        for($i =0;$i<count($teachers);$i++){

            $isBusy = DB::table('schedule_subject as ss')
                    ->leftJoin('leave_relief as lr','lr.schedule_subject_id','ss.id')
                    ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                    ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
                    ->where('sv.status',1)
                    ->where(function ($query) use ($teachers,$schedule_subject,$i, $date){
                        $query->where(function ($query) use ($teachers,$schedule_subject,$i, $date) {
                            $query->where('lr.replace_teacher_id', $teachers[$i]->user_id)
                                ->where('tl.status',1)
                                ->where('tl.date',$date)
                                ->where('ss.slot',$schedule_subject->slot)
                                ->where('ss.day',$schedule_subject->day); 
                        })->orWhere(function ($query) use ($teachers,$i, $date){
                            $query->where('tl.teacher_id', $teachers[$i]->user_id)
                            ->where('tl.date',$date)
                            ->where('tl.status',1);
                                
                        })->orWhere(function ($query) use ($teachers,$schedule_subject,$i){
                            $query->where('ss.teacher_in_charge', $teachers[$i]->user_id)
                                ->where('ss.slot',$schedule_subject->slot)
                                ->where('ss.day',$schedule_subject->day); 
                        }) ;     
                    })
                    ->exists();

            if($isBusy)
                continue;
            $info =$this->getTeacherInfo($teachers[$i]->user_id, $organization,$schedule_subject,$date);
            array_push($teacher_info,$info);
        }

        return $teacher_info;
    }

    //get free teacher, call in ajax and the result show in a combobox for each row
    public function getFreeTeacher(Request $request){

        $schedule_subject = DB::table('schedule_subject as ss')
                ->leftJoin('leave_relief as lr','lr.schedule_subject_id','ss.id')
                ->where('lr.id',$request->leave_relief_id)
                ->select('ss.*')
                ->first();
       
        $teacher_list = $this->getAvailableTeacherList($schedule_subject,$request->date,$request->organization);

        return response()->json(['free_teacher_list'=>$teacher_list]);

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

    public function getScheduleView($class_id){

        // $time_off = [];
        // $time =new stdClass();
        // $time->slot =6;
        // array_push($time_off,$time);
        // $time_off =json_encode($time_off);
        // DB::table('schedules')->update(['time_off'=>$time_off]);
        // dd($time_off);
        $slot = DB::table('schedule_subject as ss')
            ->leftJoin ('schedule_version as sv','ss.schedule_version_id','sv.id')
            ->leftJoin('schedules as s','s.id','sv.schedule_id')
            ->leftJoin('classes as c','c.id','ss.class_id')
            ->leftJoin('users as u','u.id','ss.teacher_in_charge')
            ->leftJoin('subject as sub','sub.id','ss.subject_id')
            ->where('ss.class_id',$class_id)
            ->where('sv.status',1)
            ->where('s.status',1)
            ->select('ss.id','s.id as schedule_id','c.nama as class','sub.code as subject','s.start_time','s.time_of_slot','ss.slot','s.time_off','ss.day','u.name as teacher')
            ->get();
            
        if(count($slot)>0){
            $schedule =Schedule::find($slot->first()->schedule_id);
            foreach($slot as $s){
                $s->time = $this->getSlotTime($s,$s->day,$s->slot)['time'];
                unset($s->start_time);
                unset($s->time_off);
                unset($s->time_of_slot);
            }
            
            return response()->json(['slots'=>$slot,'time_off'=>json_decode($schedule->time_off)]);
        }
        
        return response()->json(['slots'=>$slot,'time_off'=>'']);
       
    }
    public function getVersion($oid){
        $schedule = DB::table('schedules as s')
                    ->where('s.organization_id',$oid)
                    ->where('s.status',1)
                    ->select('s.id as schedule_id','s.name as schedule_name')
                    ->get();

                   // dd($schedule);
        $versionList = [];
        foreach($schedule as $s){
            
            $sv = DB::table('schedule_version as sv')
            ->where('sv.schedule_id',$s->schedule_id)
            ->where('sv.status',1)
            ->select('sv.id as version_id','sv.desc as sv_desc')
            ->latest('updated_at')->first();


            if($sv == null){
                continue;
            }
            $versionList[] = [
                'schedule_id'=>$s->schedule_id,
                'version_id' => $sv->version_id,
                'code' => $s->schedule_name . '-' . $sv->sv_desc,
            ]; 
        }


        return response()->json(['versionList'=>$versionList]);
        
    }
    public function getOrganizationByUserId()
    {

        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role pentadbir 
            //micole try
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->whereIn('role_id', [2,4, 20])
                ->whereIn('type_org',[1,2,3,15]);
            })->get();
        }
    }

    public function getClassByUserId()
    {
        $organizations = $this->getOrganizationByUserId();
        $classes = collect();

        foreach ($organizations as $organization) {
            $organizationClasses = $organization->classes;

            $classes = $classes->merge($organizationClasses);
        }
        return $classes;
    }

    public function getScheduleId()
    {
        $organizations = $this->getOrganizationByUserId();
        $schedule = Schedule::whereIn('organization_id', $organizations->pluck('id'))->get();

        return $schedule;
    }

    public function scheduleExport(Request $request)
    {
        return Excel::download(new SubjectExport($request->organ), 'schedule.xlsx');
    }

    public function scheduleImport(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'no_of_slot' => 'required|integer|min:0',
            'time_of_slot' => 'required|integer|min:0',
            'starttime' => 'required|date_format:H:i',
            'day' => 'required|array',
            'maxslot' => 'required|integer|min:0',
            'organization_id'=>'required'
            // Add other validation rules for your fields
        ]);

        // $dayOfWeekMapping = [
        //     'monday' => 1,
        //     'tuesday' => 2,
        //     'wednesday' => 3,
        //     'thursday' => 4,
        //     'friday' => 5,
        //     'saturday' => 6,
        //     'sunday' => 7,
        // ];

        // // Get selected days from the request
        // $selectedDays = $request->input('day', []);

        // // Map selected days to their numeric representation
        // $numericDays = array_map(function ($day) use ($dayOfWeekMapping) {
        //     return $dayOfWeekMapping[$day];
        // }, $selectedDays);
        // $organizations = $this->getOrganizationByUserId();
        $days = array_map('intval', $request->day);
        // Create a new Schedule instance and fill it with form data
        $schedule = new Schedule([
            'name' => $request->name,
            'number_of_slot' => $request->no_of_slot,
            'time_of_slot' => $request->time_of_slot,
            'start_time' => $request->starttime,
            'day_of_week' => json_encode($days),
            'teacher_max_slot' => $request->maxslot,
            'target' => '{"data": "ALL"}',
            'status' => 1,
            'organization_id' => $request->organization_id,
            'time_off' => json_encode([]),
            // Add other fields
        ]);

        // Save the schedule to the database
        $schedule->save();
        return redirect()->back()->with('success', 'Schedule inserted successfully!');

    }

    public function scheduleSubjectImport(Request $request){
        $request->validate([
            'schedule_id' => 'required',
            'file' => 'required|mimes:xlsx,xls',
            //'organization_id'=>'required'
        ]);

        $organizationId = Schedule::find($request->schedule_id)->organization_id;
    
        //dd($request->new_version,$request->autoInsert,$organizationId);
        $file = $request->file('file');

        $exists_version =DB::table('schedule_version')->where('schedule_id',$request->schedule_id)->where('status',1)->exists();

        if($request->new_version=="0" && $exists_version ){
            $version_id =DB::table('schedule_version')->where('schedule_id',$request->schedule_id)->where('status',1)->first()->id;
        }
        else {

            $update =DB::table('schedule_version')->where('schedule_id',$request->schedule_id)->where('status',1)->update([
                'status'=>0
            ]);

            $version_id = DB::table('schedule_version')->insertGetId([
                'desc'=>$request->desc,
                'schedule_id'=>$request->schedule_id,
                'status'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ]);
        }
       
        $autoInsert = $request->autoInsert=="on" ;
        Excel::import(new ScheduleImport($version_id,$organizationId,$autoInsert), $file);
    

        $teacherLeave =DB::table('teacher_leave as tl')
                        ->leftJoin('organization_user as ou','ou.user_id','tl.teacher_id')
                        ->where('ou.organization_id',$organizationId)
                        ->where('tl.date','>',Carbon::today())
                        ->where('tl.status',1)
                        ->select('tl.*','tl.period->fullday as isLeaveFullDay')
                        ->get();
       //dd($teacherLeave);
        foreach($teacherLeave as $leave){
            DB::table('leave_relief')->where('teacher_leave_id',$leave->id)->update(['status'=>0]);
            $this->regenerateLeaveRelief($leave);
        }
        return redirect()->back()->with('success', 'Schedules imported successfully!');

    }

    public function regenerateLeaveRelief($leave){
       
        $classRelated = DB::table('schedule_subject as ss')
                ->join('schedule_version as sv','sv.id','ss.schedule_version_id')
                ->join('schedules as s','s.id','sv.schedule_id')
                ->where('ss.day',Carbon::parse($leave->date)->dayOfWeek)
                ->where('ss.teacher_in_charge',$leave->teacher_id)
                ->where('s.status',1)
                ->where('sv.status',1)
                ->select('s.*','ss.id as schedule_subject_id','ss.day as day','ss.slot as slot')
                ->get();
                
                foreach($classRelated as $c){
                    if($leave->isLeaveFullDay == "true"){
                        //dd('true');
                        $insert = DB::table('leave_relief')->insert([
                            'teacher_leave_id'=>$leave->id,
                            'schedule_subject_id'=>$c->schedule_subject_id,
                            'status'=>1
                        ]);
                    }else{
                        //dd('false');
                        $start = Carbon::createFromFormat('H:i:s', $request->start_time);
                        $end = Carbon::createFromFormat('H:i:s', $request->end_time);
                        $time_info=$this->getSlotTime($c,$c->day,$c->slot);
                        $check = Carbon::createFromFormat('H:i:s', $time_info['time'] );

                        
                        // check if the time is between start and end
                        if ($check->between($start, $end) || $check->addMinutes($time_info['duration']-1)->between($start,$end)) {
                            $insert = DB::table('leave_relief')->insert([
                                'teacher_leave_id'=>$leave_id,
                                'schedule_subject_id'=>$c->schedule_subject_id,
                                'status'=>1
                            ]);
                        } 
                    }
                }
    }

}
