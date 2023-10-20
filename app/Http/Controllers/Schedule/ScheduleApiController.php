<?php

namespace App\Http\Controllers\Schedule;
use stdClass;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\NewNotification;

use App\User;

class ScheduleApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

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
            return response()->json([
                'id' => $user->id,
                'name' => $user->name
            ], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
         
     }

     public function getTimeOff(Request $request){
        
        $data = [
            'header' => 'hello title',
            'body' => 'hello body',
        ];
        event(new NewNotification($data));
        $datalist = [];
        $data = new stdClass();
        $data->slot = 11;
        $data->duration=20;
        $data->desc="Self revision";
        array_push($datalist,$data);
        $data = new stdClass();
        $data->slot = 5;
        $data->day=[4,5];

       
        array_push($datalist,$data);
        $data = new stdClass();
        $data->slot = 4;
       
       
       
        array_push($datalist,$data);

        $json =json_encode($datalist);
        dd($json);
        $result =json_decode($json);
        $msg=[];
        foreach($result as $r){
            $m="time off when slot ".$r->slot;
            if(isset($r->day)){
                $days = implode(",", $r->day);
                $m =$m." at ".$days;
            }
            else{
                $m =$m." at every day";
            }

            if(isset($r->duration)){
                $m =$m." is ".$r->duration." minutes";
            }
            if(isset($r->desc)){
                $m=$m." for ".$r->desc;
            }

            array_push($msg,$m);
            
        }
        return response()->json(['timeoff'=>$msg]);
     }

    public function sendNotification($id){
        $user = User::where('id', $id)->first();
 
        $notification_id = $user->device_token;
        $title = "Greeting Notification";
        $message = "Have good day!";
        $id = $user->id;
        $type = "basic";
        
        $res = send_notification_FCM($notification_id, $title, $message, $id,$type);
        
        if($res == 1){
        
            // success code
            return response()->json(["Success"]);
        
        }else{
        
            return response()->json(["failed"]);
        }
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
