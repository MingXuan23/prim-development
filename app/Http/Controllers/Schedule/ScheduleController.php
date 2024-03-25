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
use Yajra\DataTables\DataTables;

use App\Http\Controllers\Schedule\ScheduleApiController;


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

    public function getScheduleStatus($id){

        $schedule = DB::table('schedules')
                ->where('id',$id)
                ->first();

        $versionCount = DB::table('schedule_version')
                        ->where('schedule_id',$schedule->id)
                        ->count();
        //dd($versionCount);

        $desc = DB::table('schedule_version')
                    ->where('schedule_id',$schedule->id)
                    ->orderBy('created_at','desc')
                    ->first();
        
                    
        return response()->json(['schedule_status'=>$schedule->status ,'version_count'=>$versionCount ,'desc'=>$desc==null?'':$desc->desc??'No desc']);
    }

    public function manageReliefIndex()
    {
        $organization = $this->getOrganizationByUserId();
        $classes = $this->getClassByUserId();
        // $schedule = $this->getScheduleId();
        return view('manage_relief.index', compact('organization', 'classes'));
    }

    public function getTeacherOfOrg(Request $request){
        $teachers = DB::table('users as u')
                    ->leftJoin('organization_user as ou','ou.user_id','u.id')
                    ->where('ou.organization_id',$request->organization)
                    ->where('ou.role_id',5)
                    ->select('u.id as teacher_id','u.name as name')
                    ->get();

        $leaveType =DB::table('leave_type')
                    ->where('status',1)
                    ->get();
        return response()->json(['teachers'=>$teachers,'leaveType'=>$leaveType]);
    }

    public function reliefReportIndex()
    {
        $organization = $this->getOrganizationByUserId();
        $classes = $this->getClassByUserId();
        // $schedule = $this->getScheduleId();
        return view('relief_report.index', compact('organization', 'classes'));
    }

    public function datatablePendingRelief(Request $request){
        set_time_limit(300);
        $oid = $request->organization;
        $teachers='';
        $date =$request->date;
        // dd($request);
        $relief =$this->getAllRelief($oid, $date,"isQuery");

        $table = Datatables::of($relief);

        $table->addColumn('reason', function ($row) {
           $reason = $row->desc;
           // var imageLinkFunction = '';
           if ($row->image !== null) {
               // imageLinkFunction = imageLinkFunction.toString(); // Convert the function to a string
               $reason = $reason. '<a href="#" class="image-link" data-image="' .$row->image . '">(View)</a>';
           }
           return $reason;
   
        });

        $table->addColumn('combobox', function ($row) {
            return  '<select class="form-control assign_teacher" data-index="' . $row->leave_relief_id .
            '" schedule_subject_id="' . $row->schedule_subject_id . '" slot="' . $row->slot . '"></select>';
        });

       

        $table->rawColumns(['combobox','reason']);
        return $table->make(true);
    }
    //using ajax to call and get the pending relief
    public function getAllTeacher(Request $request){
        set_time_limit(300);
        $oid = $request->organization;
        $teachers='';
        $date =$request->date;
        // dd($request);
        $teachers = DB::table('organization_user as ou')
        ->leftJoin('users as u','u.id','ou.user_id')
        ->where('ou.organization_id',$oid)
        ->where('ou.role_id',5)
        ->select('ou.user_id as id','u.name')
        ->where('ou.status','<>',0)
        ->get()
        ->toArray();

        $schedule = DB::table('schedules as s')
                ->where('s.organization_id',$oid)
                ->where('s.status',1)
                ->select('s.teacher_max_slot as max_slot','s.max_relief_slot as max_relief')
                ->first();
       
        foreach($teachers as $t){
            $t->details = $this->getReliefSlot($date,$t->id,$schedule->max_slot,$schedule->max_relief);
        }
        // dd($relief);
       // $teachers = $this->getFreeTeacher($request); // New method to get available teachers
      // dd($relief,$schedule);
        return response()->json(['teachers'=>json_encode($teachers)]);

        //return response()->json(['pending_relief' => $relief, 'available_teachers' => $teachers]);
    }

    // get all pending, null and rejected relief
    public function getAllRelief($oid, $date, $isQuery=null){
        $organization = $this->getOrganizationByUserId();
        
        if($organization->contains('id', $oid)){
            $query = DB::table('leave_relief as lr')
            ->leftJoin('schedule_subject as ss','ss.id','lr.schedule_subject_id')
            ->leftJoin('classes as c','c.id','ss.class_id')
            ->leftJoin('subject as sub','sub.id','ss.subject_id')
            ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
            ->leftJoin('schedules as s','s.id','sv.schedule_id')
            ->leftJoin('users as u1','u1.id','ss.teacher_in_charge')
            ->leftJoin('users as u2','lr.replace_teacher_id','u2.id')
            ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
            ->where(function ($query) {
                $query->whereNull('lr.confirmation');
            })
            ->where('lr.status',1)
            ->where('s.organization_id',$oid)
            ->where('sv.status',1)
            ->where('ss.status',1)
            ->where('tl.date',$date)
            ->where('tl.status',1)
            ->orderBy('ss.slot')
            ->select('lr.id as leave_relief_id','lr.confirmation','ss.id as schedule_subject_id','tl.date','tl.desc'
            ,'sub.name as subject','u1.name as leave_teacher','u2.name as relief_teacher','ss.slot','ss.day','s.time_of_slot','s.start_time','s.time_off','c.nama as class_name','tl.image');
            
            if($isQuery == "isQuery" ){
                return $query;
            }
             $relief = $query->get();
            //dd($relief);
            foreach($relief as $r){
                $result=$this->getSlotTime($r,$r->day,$r->slot);
                $r->time = $result['time'];
                $r->duration =$result['duration'];
                unset($r->time_of_slot,$r->start_time,$r->time_off);
            }
            return $relief;
        }
    }

    public function getReliefReport(Request $request){
        $oid = $request->organization;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $relief = $this->getAllReliefForReport($oid, $startDate, $endDate);

        return response()->json(['relief_report' => $relief]);
    }

    public function notifyTeacher($lr_id){

        $leave_relief = DB::table('leave_relief')->where('id',$lr_id)->first();
        if($leave_relief == null || $leave_relief->replace_teacher_id ==null){
            return response()->json(['message'=>'Data Error']);
        }
        $user =User::find($leave_relief->replace_teacher_id);
        $ScheduleApiController = new ScheduleApiController();

   // Call the sendFirebaseNotification function
   $message = "Notification failed to send";
       $result = $ScheduleApiController->sendFirebaseNotification($user->id, 'Hi, ' . $user->name, 'You have a new pending relief. Please check the latest pending relief in APP');
       $result = $result->getData(true); // Convert response to array
       if (isset($result['success'])) {
           $update = DB::table('leave_relief')
           ->where('id', $data[0])
           ->increment('notification_count');

        $message = "Notification success to send";

       }
       
       return response()->json(['message'=>$message]);
    }
    public function saveRelief(Request $request){
        $reliefs = $request -> commitRelief;
        if(!$this->checkAdmin(Auth::User()->id,$request->organization)){
            return redirect()->back()->with('success', '401 Error');
        }
        $commitRelief = json_decode($request->commitRelief, true);

        $msg ='';
       foreach($commitRelief as $cr){
             $data =explode('-', $cr);
             $update = DB::table('leave_relief')->where('id',$data[0])->update([
                'replace_teacher_id'=>$data[1],
                'confirmation'=>'Pending',
                'status'=>1
             ]);

             $user =User::find($data[1]);
             $ScheduleApiController = new ScheduleApiController();

        // Call the sendFirebaseNotification function
            $result = $ScheduleApiController->sendFirebaseNotification($data[1], 'Hi, ' . $user->name, 'You have a new pending relief. Please check the latest pending relief in APP');
            $result = $result->getData(true); // Convert response to array
            if (isset($result['success'])) {
                $update = DB::table('leave_relief')
                ->where('id', $data[0])
                ->increment('notification_count');
            }
            
       }
       return redirect()->back()->with('success', $msg);
    }


    public function updateSchedule(Request $request){
        $schedule = Schedule::find($request->schedule_id);
    
        if(!$this->checkAdmin(Auth::id(),$schedule->organization_id)){
            return redirect()->back()->with('error', 'You have not authority to change it');
        }

        //dd($request->schedule_status);
        $schedule->status = $request->schedule_status == "true";
        $schedule->save();

        if($request->schedule_status != "true"){
            $leave_relief = DB::table('leave_relief as lr')
            ->leftJoin('schedule_subject as ss','ss.id','lr.schedule_subject_id')
            ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
            ->where('sv.schedule_id',$schedule->id)
            ->select('lr.id')
            ->get();

            foreach($leave_relief as $lr){

                DB::table('leave_relief')->where('id',$lr->id)->update(['status'=>0]);
            }
        }
       
        return redirect()->back()->with('success', 'Update schedule successfully');
    }

    public function getAllReliefForReport($oid, $startDate, $endDate)
    {
        $organization = $this->getOrganizationByUserId();
    
        if ($organization->contains('id', $oid)) {
            $query = DB::table('leave_relief as lr')
                ->leftJoin('schedule_subject as ss', 'ss.id', 'lr.schedule_subject_id')
                ->leftJoin('classes as c', 'c.id', 'ss.class_id')
                ->leftJoin('subject as sub', 'sub.id', 'ss.subject_id')
                ->leftJoin('schedule_version as sv', 'sv.id', 'ss.schedule_version_id')
                ->leftJoin('schedules as s', 's.id', 'sv.schedule_id')
                ->leftJoin('users as u1', 'u1.id', 'ss.teacher_in_charge')
                ->leftJoin('users as u2', 'lr.replace_teacher_id', 'u2.id')
                ->leftJoin('teacher_leave as tl', 'tl.id', 'lr.teacher_leave_id')
            
                ->where(function ($query) {
                    $query->where('lr.confirmation', 'Rejected')
                        ->orWhere('lr.confirmation', 'Confirmed')
                        ->orWhere('lr.confirmation', 'Pending')
                        ->orWhereNull('lr.confirmation');
                    
                })
                ->whereBetween('tl.date', [$startDate, $endDate])
                ->where('lr.status', 1)
                ->where('tl.status',1)
                ->where('s.organization_id', $oid)
                ->orderBy('tl.date')
                ->select('lr.id as leave_relief_id', 'lr.confirmation', 'ss.id as schedule_subject_id', 'tl.date', 'tl.desc', 'sub.name as subject', 'u1.name as leave_teacher', 'u2.name as relief_teacher', 'ss.slot', 'ss.day', 's.time_of_slot', 's.start_time', 's.time_off', 'c.nama as class_name','lr.notification_count');
    
            $relief = $query->get();
                    // dd($endDate);
            foreach ($relief as $r) {
                $result = $this->getSlotTime($r, $r->day, $r->slot);
                $r->time = $result['time'];
                $r->duration = $result['duration'];
                unset($r->time_of_slot, $r->start_time, $r->time_off);
            }
           // dd($relief);
            return $relief;
        }
    }    

    //to get the teacher burden information
    public function getTeacherInfo($teacher_id,$start_date,$end_date ,$isDetail){
        $startDate = Carbon::parse($start_date)->startOfWeek();
        $endDate = Carbon::parse($end_date)->endOfWeek();
        $numberOfWeeks = $startDate->diffInWeeks($endDate) +1;

        $teacher =new stdClass();
        $teacher->id =$teacher_id;
        $teacher->name = User::find($teacher_id)->name;
        $teacher->normal_class = count(DB::table('schedule_subject as ss' )
                        ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
                        ->where('sv.status',1)
                        ->where('ss.status',1)
                        ->where('ss.teacher_in_charge',$teacher_id)
                        ->groupBy(['ss.day','ss.slot'])
                        ->get());  
       
       $teacher->relief_class = DB::table('leave_relief as lr')
                        ->leftJoin('teacher_leave as tl', 'tl.id', 'lr.teacher_leave_id')
                        ->leftJoin('schedule_subject as ss', 'ss.id', 'lr.schedule_subject_id')
                        ->where('lr.status', 1)
                        ->where('lr.replace_teacher_id', $teacher_id)
                        ->whereBetween('tl.date', [$startDate, $endDate])
                        ->groupBy(['tl.date', 'ss.slot'])  // Group by both date and slot
                        ->count();
        //dd($teacher);
        $teacher->leave_class =DB::table('leave_relief as lr')
                    ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                    ->where('lr.status',1)
                    ->where('tl.teacher_id',$teacher_id)
                    ->whereBetween('tl.date',[$startDate,$endDate])
                    ->count('lr.id');
       
        if(!$isDetail){
            return $teacher ->normal_class  - $teacher->leave_class + $teacher->relief_class;
        }else{
            $teacher->maxSlot = DB::table('schedules as s' )
                                ->leftJoin('schedule_version as sv','sv.schedule_id','s.id')
                                ->where('sv.status',1)
                                ->select('s.teacher_max_slot','s.max_relief_slot','s.day_of_week')
                                ->first();
                    
             $dayList = json_decode($teacher->maxSlot->day_of_week);
             $totalDays = $startDate->diffInDaysFiltered(function (Carbon $date) use ($dayList) {
                return in_array($date->dayOfWeek, $dayList);
            }, $endDate);

            //dd($totalDays,$teacher,$dayList);
            $teacher->maxRelief = $teacher->maxSlot ==null?0:   $teacher->maxSlot->max_relief_slot * $totalDays;
            $teacher->maxSlot = $teacher->maxSlot ==null?0:   $teacher->maxSlot->teacher_max_slot * $totalDays;
            
            
            return $teacher;
        }
        //return $teacher;

    }

    public function getReliefSlot($date,$teacher_id,$maxSlot,$maxRelief){
        $carbon_date = Carbon::parse($date);
        $teacher= new stdClass();
        $teacher->name = User::find($teacher_id)->name;
        $teacher->normal_class = DB::table('schedule_subject as ss' )
                        ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
                        ->leftJoin('schedules as s','s.id','sv.schedule_id')
                        ->where('sv.status',1)
                        ->where('ss.status',1)
                        ->where('s.status',1)
                        ->where('ss.teacher_in_charge',$teacher_id)
                        ->where('ss.day',$carbon_date->dayOfWeek)
                        ->count('ss.id');

        $teacher->relief_class = DB::table('leave_relief as lr')
                                ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                                ->leftJoin('schedule_subject as ss','ss.id','lr.schedule_subject_id')
                                ->where('lr.status',1)
                                ->where('lr.replace_teacher_id',$teacher_id)
                                ->where('tl.date',$date)
                                ->groupBy(['ss.slot','tl.date'])
                                ->get()
                                ->count();
        //dd($teacher);
        
        $teacher->leave_class =DB::table('leave_relief as lr')
                    ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                    ->where('lr.status',1)
                    ->where('tl.teacher_id',$teacher_id)
                    ->where('tl.date',$date)
                    ->count('lr.id');
        $teacher->max_slot = $maxSlot;
        $teacher ->max_relief =$maxRelief == null?999:$maxRelief;
        //dd($maxRelief,$teacher->max_relief,$maxRelief == null);

        $teacher->busySlot = $teacher->normal_class - $teacher->leave_class + $teacher->relief_class;
        $teacher ->remaining_relief = $teacher->max_relief - $teacher->relief_class;
        if($teacher->busySlot >= $teacher->max_slot || $teacher->relief_class >= $teacher->max_relief) {
            $teacher ->remaining_relief =0;
        } 
        return $teacher;
    }

    public function checkSameClass($class_id,$teacher_id){
        $isSameClass = DB::table('schedule_subject as ss')
                    ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
                    ->leftJoin('schedules as s','s.id','sv.schedule_id')
                    ->where('sv.status',1)
                    ->where('ss.status',1)
                    ->where('s.status',1)
                    ->where('ss.class_id',$class_id)
                    ->where('ss.teacher_in_charge',$teacher_id)
                    ->exists();

        return $isSameClass == true? 1:5; //smaller value will sort first
    }

    public function checkSameSubject($subject_id,$teacher_id){
        $isSameSubject = DB::table('schedule_subject as ss')
            ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
            ->leftJoin('schedules as s','s.id','sv.schedule_id')
            ->where('sv.status',1)
            ->where('ss.status',1)
            ->where('s.status',1)
            ->where('ss.subject_id',$subject_id)
            ->where('ss.teacher_in_charge',$teacher_id)
            ->exists();
        return $isSameSubject == true? 1:5;
    }
    //to auto suggesstion
    public function autoSuggestRelief(Request $request){
        set_time_limit(300);
        //$request->date = Carbon::now();
        $pendingRelief = $request -> pendingRelief;
        $date = $request->date;
        $organization =$request->organization;
        $criteria = $request->criteria;
        //dd($criteria);
        $relief_draft =[];
        if($pendingRelief == null){
            return response()->json(['relief_draft'=>$relief_draft]);
        }
        $teachers = json_decode($request->teachers);

        $assignedTeachers = [];
        $teacherSlots = [];
        //dd($pendingRelief);
        $current_slot = -1;

        foreach($pendingRelief as $p){
            $data =explode('-', $p);
            $leave_relief_id =$data[0];
            $schedule_subject = DB::table('schedule_subject as ss')
                    ->join('schedule_version as sv','sv.id','ss.schedule_version_id')
                    ->join('schedules as s','s.id','sv.schedule_id')
                    ->where('ss.id',$data[1])
                    ->select('ss.*','s.teacher_max_slot as max_slot','s.max_relief_slot as max_relief')
                    ->first();
            //dd($schedule_subject);
            $teacherList = $this->getAvailableTeacherList($schedule_subject,$date,$organization,$teachers);
 // criteria
            
            //dd($teacherList);
            switch($criteria){
                case 'Beban Guru':
                    // usort($teacherList, function ($a, $b) use( $date) {
                    //     // Your custom comparison logic here
                    //     return $this->getTeacherInfo($a->id, $date,$date,false) - $this->getTeacherInfo($b->id, $date,$date,false);
                    // });

                    usort($teacherList, function ($a, $b) use( $date,$schedule_subject) {
                        // Your custom comparison logic here
                        $comparison = $b->details->remaining_relief -$a->details->remaining_relief ;
                        if($comparison!==0){
                            return $comparison;
                        }
                        return $a->details->busySlot - $b->details->busySlot;
                    });
                    break;
                case 'Kelas':
                    usort($teacherList, function ($a, $b) use ($date, $schedule_subject) {
                        $comparison = $this->checkSameClass($schedule_subject->class_id, $a->id)
                            - $this->checkSameClass($schedule_subject->class_id, $b->id);
                    
                        if ($comparison !== 0) {
                            return $comparison;
                        }
                    
                        // If classes are the same, compare using getTeacherInfo
                        $comparison = $b->details->remaining_relief -$a->details->remaining_relief ;
                        if($comparison!==0){
                            return $comparison;
                        }
                        return $a->details->busySlot - $b->details->busySlot;
                    });
                    break;
                case 'Subjek':
                    usort($teacherList, function ($a, $b) use ($date, $schedule_subject) {
                        $comparison = $this->checkSameSubject($schedule_subject->subject_id, $a->id)
                            - $this->checkSameSubject($schedule_subject->subject_id, $b->id);
                    
                        if ($comparison !== 0) {
                            return $comparison;
                        }
                    
                        // If classes are the same, compare using getTeacherInfo
                        $comparison = $b->details->remaining_relief -$a->details->remaining_relief ;
                        if($comparison!==0){
                            return $comparison;
                        }
                        return $a->details->busySlot - $b->details->busySlot;
                    });
                    break;
                default:
                    return response()->json(['relief_draft'=>$relief_draft]);
                    break;
            }

           // dd($before,$teacherList);
           //dd(count( $assignedTeachers),$assignedTeachers);
            foreach($teacherList as $t){ 
               // dd($t);
                if(!in_array($t->id,$assignedTeachers )) {
                    //continue with proccess below
                }          
                else if(array_count_values($assignedTeachers)[$t->id] >= $t ->details->remaining_relief || $t->details->remaining_relief <= 0)
                    continue;

                else if($current_slot == $schedule_subject ->slot && in_array($t->id,$teacherSlots)){
                    continue;
                }
                // else{
                //     dd($current_slot,$schedule_subject ->slot,$t->id  ,$assignedTeachers[count($assignedTeachers) - 1]);
                // }
                if($current_slot != $schedule_subject ->slot){
                    $teacherSlots = [];
                }

                // if($leave_relief_id == 347){
                //     dd($t,array_count_values($assignedTeachers)[$t->id] , $t ->details->remaining_relief);
                // }
                $current_slot =$schedule_subject ->slot;
                $draft = new stdClass();
                $draft->teacher_id = $t->id;
                $draft->teacher_name =$t->name;

                $draft->schedule_subject_id=$schedule_subject->id;
                $draft->leave_relief_id = $leave_relief_id;
                array_push($relief_draft, $draft);
                $assignedTeachers[] = $t->id;
                $teacherSlots[]=$t->id;
                break;
            }

        }
        //dd($assignedTeachers,array_count_values($assignedTeachers)[22044],array_count_values($assignedTeachers)[22044] > 1);
        return response()->json(['relief_draft'=>$relief_draft,'assignTeacher'=>$assignedTeachers]);
        
       
    }

    //get all teacher available for the moment
    public function getAvailableTeacherList($schedule_subject,$date,$organization,$teachers){
        if($teachers == null){
            $teachers = DB::table('organization_user as ou')
            ->leftJoin('users as u','u.id','ou.user_id')
            ->where('ou.organization_id',$organization)
            ->where('ou.role_id',5)
            ->select('ou.user_id as id','u.name')
            ->get()
            ->toArray();
        }

        $teacher_info =[];
        //dd($teachers);
        for($i =0;$i<count($teachers);$i++){
           //dd( $teachers[$i]->id);
            $isBusy = DB::table('schedule_subject as ss')
                    ->leftJoin('leave_relief as lr','lr.schedule_subject_id','ss.id')
                    ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                    ->leftJoin('schedule_version as sv','sv.id','ss.schedule_version_id')
                    ->leftJoin('schedules as s','s.id','sv.schedule_id')
                    ->where('ss.status',1)
                    ->where('sv.status',1)
                    ->where('s.status',1)
                    ->where(function ($query) use ($teachers,$schedule_subject,$i, $date){
                        $query->where(function ($query) use ($teachers,$schedule_subject,$i, $date) {
                            $query->where('lr.replace_teacher_id', $teachers[$i]->id)
                                ->where('tl.status',1)
                                ->where('lr.status',1)
                                ->where('tl.date',$date)
                                ->where('ss.slot',$schedule_subject->slot)
                                ->where('ss.day',$schedule_subject->day); 
                        })->orWhere(function ($query) use ($teachers,$i, $date){
                            $query->where('tl.teacher_id', $teachers[$i]->id)
                            ->where('tl.date',$date)
                            ->where('tl.status',1);
                                
                        })->orWhere(function ($query) use ($teachers,$schedule_subject,$i){
                            $query->where('ss.teacher_in_charge', $teachers[$i]->id)
                                ->where('ss.slot',$schedule_subject->slot)
                                ->where('ss.day',$schedule_subject->day); 
                        }) ;     
                    })
                    ->exists();

            if($isBusy)
                continue;
            //dd($date);
            $info = $teachers[$i];
            array_push($teacher_info,$info);
        }

        return $teacher_info;
    }

    //get free teacher, call in ajax and the result show in a combobox for each row
    public function getFreeTeacher(Request $request){

        $schedule_subject = DB::table('schedule_subject as ss')
                ->leftJoin('leave_relief as lr','lr.schedule_subject_id','ss.id')
                ->where('lr.id',$request->leave_relief_id)
                ->where('ss.status',1)
                ->select('ss.*')
                ->first();
       
        $teacher_list = $this->getAvailableTeacherList($schedule_subject,$request->date,$request->organization,null);

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
        $slots = DB::table('schedule_subject as ss')
        ->leftJoin('schedule_version as sv', 'ss.schedule_version_id', 'sv.id')
        ->leftJoin('schedules as s', 's.id', 'sv.schedule_id')
        ->leftJoin('classes as c', 'c.id', 'ss.class_id')
        ->leftJoin('users as u', 'u.id', 'ss.teacher_in_charge')
        ->leftJoin('subject as sub', 'sub.id', 'ss.subject_id')
        ->where('ss.class_id', $class_id)
        ->where('sv.status', 1)
        ->where('ss.status',1)
        ->where('s.status', 1)
        ->orderBy('ss.day','asc')
        ->orderBy('ss.slot','asc')
        ->select('ss.id', 's.id as schedule_id', 'c.nama as class', 'sub.code as subject', 's.start_time', 's.time_of_slot', 'ss.slot', 's.time_off', 'ss.day', 'u.name as teacher')
        ->get();

    if (count($slots) > 0) {
        $timeOff = [];
        foreach ($slots as $slot) {
            $slot->time = $this->getSlotTime($slot, $slot->day, $slot->slot)['time'];
            unset($slot->start_time);
            unset($slot->time_off);
            // unset($slot->time_of_slot);

            // Fetch the corresponding schedule_id for each slot
            $schedule = Schedule::find($slot->schedule_id);
            
            // Add time_off data to the $timeOff array
            if ($schedule && $schedule->time_off) {
                $timeOff = json_decode($schedule->time_off);
            }
        }

        return response()->json(['slots' => $slots, 'time_off' => $timeOff]);
    }

    return response()->json(['slots' => $slots, 'time_off' => '']);
       
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
            'organization_id'=>'required',
            'maxRelief' => 'required'
            // Add other validation rules for your fields
        ]);
       
        $time_off =[];
        $maxRelief = $request -> maxRelief;
        if($request->time_off != ''){
            $slots =explode(',',$request->time_off);
            foreach ($slots as $slot) {
               if(!is_numeric($slot)){
                    return redirect()->back()->with('error', 'Invalid Format');
               }
               $t = new stdClass();
               $t->slot = floatval($slot);

               if($t->slot>$request->no_of_slot || $t->slot<=0){
                    return redirect()->back()->with('error', 'Invalid Time off value');
               }
                array_push($time_off,$t);

            }
        }
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
            'time_off' => json_encode($time_off),
            'max_relief_slot'=> $maxRelief
            // Add other fields
        ]);

        //dd($time_off,json_encode($time_off));

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
                        ->where('tl.date','>=',Carbon::today())
                        ->where('tl.status',1)
                        ->select('tl.*','tl.period->fullday as isLeaveFullDay')
                        ->get();
       //dd($teacherLeave);
      // dd($teacherLeave);
        foreach($teacherLeave as $leave){
            DB::table('leave_relief')->where('teacher_leave_id',$leave->id)->update(['status'=>0]);
            $this->regenerateLeaveRelief($leave);
        }
        return redirect()->back()->with('success', 'Schedules imported successfully!');

    }

    public function regenerateLeaveRelief($leave){
        $date = Carbon::createFromDate($leave->date);
        $classRelated = DB::table('schedule_subject as ss')
                ->join('schedule_version as sv','sv.id','ss.schedule_version_id')
                ->join('schedules as s','s.id','sv.schedule_id')
                ->where('ss.day',Carbon::parse($leave->date)->dayOfWeek)
                ->where('ss.teacher_in_charge',$leave->teacher_id)
                ->where('s.status',1)
                ->where('sv.status',1)
                ->where('ss.status',1)
                ->select('s.*','ss.id as schedule_subject_id','ss.day as day','ss.slot as slot')
                ->get();

                foreach($classRelated as $c){
                    $time_info=$this->getSlotTime($c,$c->day,$c->slot);
                    $check = Carbon::createFromFormat('H:i:s', $time_info['time'] )->addMinutes($time_info['duration']);
                    $before = Carbon::createFromFormat('H:i:s', $time_info['time'] );
                    //is today and over the time 
                    if ($date->isToday() &&  now()->gt($check->addMinutes($time_info['duration']-1))) {
                        continue;
                    }

                    $continue =DB::table('leave_relief as lr')
                    ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                    ->where('lr.schedule_subject_id',$c->schedule_subject_id)
                    ->where('lr.status',1)
                    ->where('tl.date',$date)
                    ->where('tl.status',1)
                    ->exists();

                    if($continue)
                            continue;
                    if($leave->isLeaveFullDay == "true"){
                        //dd('true');
                        $insert = DB::table('leave_relief')->insert([
                            'teacher_leave_id'=>$leave->id,
                            'schedule_subject_id'=>$c->schedule_subject_id,
                            'status'=>1
                        ]);
                    }else{
                        //dd('false');
                        $period = json_decode($leave->period);
                        $start = Carbon::createFromFormat('H:i:s', $period->start_time);
                        $end = Carbon::createFromFormat('H:i:s', $period->end_time);
                        if ($start->between($before, $check) 
                        || $end->between($before,$check)) {
                            $insert = DB::table('leave_relief')->insert([
                                'teacher_leave_id'=>$leave->id,
                                'schedule_subject_id'=>$c->schedule_subject_id,
                                'status'=>1
                            ]);
                        } 
                        // check if the time is between start and end
                        // if ($check->between($start, $end) || $check->addMinutes($time_info['duration']-1)->between($start,$end)) {
                        //     $insert = DB::table('leave_relief')->insert([
                        //         'teacher_leave_id'=>$leave->id,
                        //         'schedule_subject_id'=>$c->schedule_subject_id,
                        //         'status'=>1
                        //     ]);
                        // } 
                    }
                }
    }

    public function checkAdmin($userId,$schoolId){
        return DB::table('organizations as o')
                ->join('organization_user as ou','ou.organization_id','o.id')
                ->whereIn('ou.role_id',[2,4,7,20])
                ->where('o.id',$schoolId)
                ->where('ou.status',1)
                ->where('ou.user_id',$userId)
                ->exists();
     }

     public function sendFirebaseNotification($id,$title,$message)
     {  $user =User::find($id);

       // dd($user);

       //dd($user);
        if($user->device_token){

            //$device_token =[];
            $url = 'https://fcm.googleapis.com/v1/projects/prim-notification/messages:send';
            //array_push($device_token,$user->device_token);
       // $serverKey = getenv('FCM_SERVER_KEY');
        //$serverKey = getenv('PRODUCTION_BE_URL');
        
       
        // $data = [
        //     "token" => $device_token,
        //     "notification" => [
        //         "title" => $title,
        //         "body" =>$message,
        //     ]
        // ];

        $data = [
            "message" => [
                "token" => $user->device_token,
                "notification" => [
                    "body" => $message,
                    "title" =>  $title
                ]
            ]
        ];

        $encodedData = json_encode($data);

        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),
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
        //dd($result);

        if (isset($result['success'])) {
            return response()->json(["success"=>"success"]);
        } else {
            // Log error or handle it accordingly
            return response()->json(["failed" =>"falied"]);
        }
        }
        return response()->json(["failed"=>"no device id"]);
     }


     public function addTeacherLeave(Request $request){
        $request->validate([
            'image' => 'nullable|mimes:jpeg,png,gif',
            //'organization_id'=>'required'
        ]);

        if ($request->hasFile('image')) {
            if (!$this->isImage($request->file('image'))) {
                return response()->json(['error' => 'Image file type must be an image only'], 401);
            }
        }

        $period = new stdClass();
        // dd($request);
        $date = Carbon::createFromDate($request->date);
        if($date < Carbon::today()){
            return response()->json(['error' => 'Invalid Date'], 401);
        }
        // dd($date,$request);
        //dd($date,$request->isLeaveFullDay);
        if($request->isLeaveFullDay == "on"){
            $period->fullday=true;
            $period->start_time= "";
            $period->end_time="";
            
        }else{
            $period->fullday=false;
            $period->start_time= $request->starttime.':00';
            $period->end_time=$request->endtime.':00';
            $start = Carbon::createFromFormat('H:i:s', $request->starttime.':00')->addMinutes(1);
            $end = Carbon::createFromFormat('H:i:s', $request->endtime.':00')->addMinutes(-1);


        }

        $period = json_encode($period);
        $user = User::find($request->selectedTeacher);

        if(! DB::table('leave_type')->where('id',$request->reason)->exists()){
            return response()->json(['error' => 'Leave Type value error'], 401);
        }
        //dd($request->starttime);
        if($user){
            //dd($request->start_time);
            $existConflict = DB::table('teacher_leave')
            ->where('date', $date)
            ->where('status', 1)
            ->where('teacher_id', $user->id)
            ->where(function ($query) use ($request) {
                $query->where('period->fullday', true)
                    ->orWhere(function ($query) use ($request) {
                        if($request->starttime !=null && $request->endtime!=null){
                            $query->where('period->fullday', false)
                            ->where('period->end_time', '>', $request->starttime) //700-800 /800-801
                            ->where('period->start_time', '<', $request->endtime);
                        }
                       
                    });
            })
            ->exists();
       
        if($existConflict){
            return redirect()->back()->with('error','The selected time is conflict with the record before');
            // return response()->json(['error' => 'The selected time is conflict with the record before'], 401);
        }
       // $image = $request->input('image');
       $str = $user->id.'_' .time();
       $filename = $request->image;
        if (!is_null($request->image)) {
            
            $extension =  $request->image->extension();
            $storagePath  =    $request->image ->move(public_path('schedule_leave_image'), $str . '.' . $extension);
            $filename = basename($storagePath);
            //dd($request->image);

        }
    
        
        $leave_id =  DB::table('teacher_leave')->insertGetId([
            
                'period'=>$period,
                'date'=>$date,
                'desc'=>  $request->note,
                'status'=>1,
                'teacher_id'=>$user->id,
                'image'=>$filename ,
                'leave_type_id'=>$request->reason
    
            ]);

            
            $classRelated = DB::table('schedule_subject as ss')
            ->join('schedule_version as sv','sv.id','ss.schedule_version_id')
            ->join('schedules as s','s.id','sv.schedule_id')
            ->where('ss.day',$date->dayOfWeek==0?7:$date->dayOfWeek)
            ->where('ss.teacher_in_charge',$user->id)
            ->where('s.status',1)
            ->where('ss.status',1)
            ->where('sv.status',1)
            ->select('s.*','ss.id as schedule_subject_id','ss.day as day','ss.slot as slot')
            ->get();

            $reliefRelated = DB::table('schedule_subject as ss')
                ->join('leave_relief as lr','lr.schedule_subject_id','ss.id')
                ->join('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                ->join('schedule_version as sv','sv.id','ss.schedule_version_id')
                ->join('schedules as s','s.id','sv.schedule_id')
                ->where('ss.day',$date->dayOfWeek)
                ->where('lr.replace_teacher_id',$user->id)
                ->where('tl.date',$request->date)
                ->where('lr.status',1)
                ->whereIn('lr.confirmation',['Confirmed','Pending'])
                ->where('s.status',1)
                ->where('sv.status',1)
                ->where('ss.status',1)
                ->select('s.*','ss.id as schedule_subject_id','ss.day as day','ss.slot as slot','lr.id as lrid')
                ->get();

            //dd($classRelated,$date->dayOfWeek);
            //dd($classRelated);
            
            foreach($classRelated as $c){
                $time_info=$this->getSlotTime($c,$c->day,$c->slot);
                $check = Carbon::createFromFormat('H:i:s', $time_info['time'] )->addMinutes($time_info['duration']);
                $before = Carbon::createFromFormat('H:i:s', $time_info['time'] );

                //is today and over the time 
                if ($date->isToday() &&  now()->gt($check)) {
                    continue;
                }

                $continue =DB::table('leave_relief as lr')
                        ->leftJoin('teacher_leave as tl','tl.id','lr.teacher_leave_id')
                        ->where('lr.schedule_subject_id',$c->schedule_subject_id)
                        ->where('lr.status',1)
                        ->where('tl.date',$date)
                        ->where('tl.status',1)
                        ->exists();
                if($continue)
                        continue;
               // dd($request->isLeaveFullDay == "on");
                if($request->isLeaveFullDay== "on"){
                    $insert = DB::table('leave_relief')->insert([
                        'teacher_leave_id'=>$leave_id,
                        'schedule_subject_id'=>$c->schedule_subject_id,
                        'status'=>1
                    ]);
                }else{
                    // check if the time is between start and end
                    //dd($check->between($start, $end) && $check->addMinutes($time_info['duration']-1)->between($start,$end));
                    // if($c->slot == 4)
                    //dd($check,$start,$end,$check->addMinutes($time_info['duration']),$check->between($start, $end),$check->addMinutes($time_info['duration'])->between($start,$end));
                    if ($start->between($before, $check) 
                    || $end->between($before,$check)) {
                        //dd("insert"); 
                        $insert = DB::table('leave_relief')->insert([
                            'teacher_leave_id'=>$leave_id,
                            'schedule_subject_id'=>$c->schedule_subject_id,
                            'status'=>1
                        ]);
                        //dd($insert);
                    } 
                }
            }

            foreach($reliefRelated as $c){

                $time_info=$this->getSlotTime($c,$c->day,$c->slot);
                $check = Carbon::createFromFormat('H:i:s', $time_info['time'] )->addMinutes($time_info['duration']);
                $before = Carbon::createFromFormat('H:i:s', $time_info['time'] );

                //is today and over the time 
                if ($date->isToday() &&  now()->gt($check)) {
                    continue;
                }
                $duplicate_row = DB::table('leave_relief')->where('id',$c->lrid)->first();
                if($request->isLeaveFullDay== "on"){
                    
                    DB::table('leave_relief')->where('id',$c->lrid)->update(['Confirmation'=>'Rejected']);
                   
                   $insert = DB::table('leave_relief')->insert([
                    'teacher_leave_id'=>$duplicate_row->teacher_leave_id,
                    'schedule_subject_id'=>$duplicate_row->schedule_subject_id,
                    'status'=>1
                    ]);
                }else{
                    if ($start->between($before, $check) 
                    || $end->between($before,$check)) {

                        DB::table('leave_relief')->where('id',$c->lrid)->update(['Confirmation'=>'Rejected']);
                        $insert = DB::table('leave_relief')->insert([
                            'teacher_leave_id'=>$duplicate_row->teacher_leave_id,
                            'schedule_subject_id'=>$duplicate_row->schedule_subject_id,
                            'status'=>1
                            ]);
                    } 
                }
            }

            $count = DB::table('leave_relief')->where('teacher_leave_id',$leave_id)->count();
            //dd($count);
            return redirect()->back()->with('success','Leave added successfully');
            
        }
        return response()->json(['error' => 'This user did not exist'], 401);
     }

     // Function to check if the file is an image
    private function isImage($file) {
        $allowedImageTypes = ['jpeg', 'png', 'gif', 'jpg'];

        if ($file && $file->isValid()) {
            $extension = $file->getClientOriginalExtension();

            return in_array(strtolower($extension), $allowedImageTypes);
        }

        return false;
    }

     public function getTeacherSlot(Request $request){
        $teachers = DB::table('users as u')
        ->leftJoin('organization_user as ou','ou.user_id','u.id')
        ->where('ou.organization_id',$request->organization)
        ->where('ou.role_id',5)
        ->where('u.name','LIKE','%'.$request->teacher_name.'%')
        ->select('u.id as teacher_id','u.name as name')
        ->get();

        $array = [];
        foreach($teachers as $t){
            array_push($array,$this->getTeacherInfo($t->teacher_id,$request->start_date,$request->end_date,true));
        }

        $startDate = Carbon::parse($request->start_date)->startOfWeek();
        $endDate = Carbon::parse($request->end_date)->endOfWeek();
        $numberOfWeeks = $startDate->diffInWeeks($endDate) +1;
        return response()->json(['teachers'=>$array,'NumberOfWeek'=>$numberOfWeeks]);
     }

     public function adminManageRelief(Request $request)
     {
         // Receive data from the AJAX request
         //dd("error");
         $reliefId = $request->input('relief_id');
         $confirmationStatus = $request->input('confirmation_status');
        // dd ($reliefId,$confirmationStatus);
        //dd($reliefId);
         try {
            // Update the confirmation status in the database
            DB::table('leave_relief')
            ->where('id', $reliefId)
            ->update(['confirmation' => $confirmationStatus]);

            $duplicate_row = DB::table('leave_relief')->where('id',$reliefId)->first();

            if($confirmationStatus =='Rejected'){
           
                $insert = DB::table('leave_relief')->insert([
                    'teacher_leave_id'=>$duplicate_row->teacher_leave_id,
                    'schedule_subject_id'=>$duplicate_row->schedule_subject_id,
                    'status'=>1
    
                ]); //generate new record for admin

               // dd($insert);
            }
            //dd($reliefId);
            //dd($re)
             // Return a success response
             return response()->json(['message' => 'Relief confirmation status updated successfully']);
         } catch (\Exception $e) {
             // Return an error response if an exception occurs
             //dd($e);
             return response()->json(['error' => 'Failed to update relief confirmation status'], 500);
         }
     }

     public function leaveReliefPolicy(){
        return view('schedule.policy');
     }
}
