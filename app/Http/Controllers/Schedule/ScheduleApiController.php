<?php

namespace App\Http\Controllers\Schedule;
use stdClass;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;



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
                'device_tokne'=>$user->device_token
                

            ], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
         
     }

     public function getTimeOff(Request $request){
        
        $data = [
            'header' => 'hello title',
            'body' => 'hello body',
        ];
       
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

     public function sendNotification($id)
     {  $user =User::find($id);

       // dd($user);
        if($user->device_token){
            $accessToken = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQD0HvLDejwk0RuM\nJzdceeF/ygASH/gWTZqTYN28VBD/hYIeheN3n9L9N6EuN7PgcMfEcHqtNSF+LPb1\nuvNum/+3rW6OhJNmPBR5k9MmDrVXr2hvdTsHTdzHCDTcHBRmK7boUXSCw21q2VL9\n5IhhLVziEP4jWfC9FgV0cAjfmndIzuGgzuher01k3kAo801nYvFlHjP3OvD4598X\ns/ndAhNb8btk65sRz2ImUtFVL+jiY9geyb8vsyabB6U5NZBXKRT6MoVeXV5pGsrt\n+MWVDEf8TuED4ZM7UlS6o8Sr0PHQDm6LDzaGj13oI6nk6rGWnlV9R9yNNOJ3ttv6\nytk/6apbAgMBAAECggEAEpk9A5mPdXqc76uZMylx/atlH/xhiUl2Sl4p5ow9E0qX\npD2tG9MIXxRa6kuCH8pX3eZ34jRXDebdFdGddELcU6EZ+C+vjy1qneyePJsIQ9rw\nSPWUfrT26g78//v/rd0MvVxfVQsQjgBqqz87CLRNDEghJI5Yof9IgRt8AZUiG2DJ\nhhbyqeEz9JNYxa25ttjknmDpZ7DnJwwAAPB0U7fIyOERd6nIv3+ySXfi0ZTm9wuH\n+6KbvbeKwRwg3ShO75ztyrwRwom7QPOSYdnLh6w0ogoL9GmlGLrCUszIeLx2KTye\nwc5Q5G/p3DIqpvJaFerAYwZdpUeXqcOxOM4ndfyaqQKBgQD7kvncg583dyPaSsQR\n8Smixh6+7UkBesGV/k5+BzakLxX6DsePrk8DgNZrH7ZZ8HA+pJiIikkV1/bHHdKR\nPuGpxRCspb24bqFH5TI/D17k+ncEqgWTcIrox89yu3BIAAxRyOIzG2FMEH4Uunzc\nhRdGaA2NzF8ErE6CpS12Otql9wKBgQD4amelHav12AZQR/vjmph+szkZBsAbfI1j\n9/64r5drPpsij8Hjo3dBGv3yxapXUWTooeXF/EETPamoqsj4o30hfvh/BATruWty\nPypvRacDbI5jetj/0v1ofBQWl0VKGoRyDYqmrkDfBsOivzgzSs53BbSBU06DBnRY\n5hRS3Kc1vQKBgHtzhGlRra/qJw3X4p9rWKMn1a6bglfXhWe1g48UuxuWf5JV7lfz\nkZKGhrHKvhEki/AxlShrs7GkaNUNLWdZFCPbMHOIYbE/mKVPM3j+cfKrdfwz8siH\nUaMpagNDN7YdT+5SRa4OoZBSB4zkdqFALku+g+gxge8pHt29cLGz79fBAoGAByzj\no4RQ5EACJq19nBxqDTbWDmAAioq1ds7B/8mqoQFk78GhQxcEqc/CyBFnkzAZrxKG\nFYrswkaEsQeF2JC4W5BUUy7liX2ImfszGZW0dkfbcQoqXHFWun7jAagK61IKw1Sa\nzae43fhPDFNjpy+g+RUkGpwyZ1x3Xd3/dklDVy0CgYEAj/E/GxPhbE6SaCz9XzV2\n2u+ISa7yGgF5Jx4sJeLW0SnrBp2hgfoJC9eoYgvt05Tv28KFOmOVs5olKJi9yaBJ\n1AcQfPbIJTw9KvBtFMZM6Ii6hfZNWlTZjiwUg6bqaTt9MT0/cE6ckTuKcLSmlp0u\nQy+tmc59MfUgPdfS9FfLVpE';
            $fcmUrl = 'https://fcm.googleapis.com/v1/projects/prim-notification/messages:send';
    
            $messageData = [
                'message' => [
                    'notification' => [
                        'title' => 'FCM Message',
                        'body' => 'This is an FCM Message',
                    ],
                    'token' => $user->device_token,
                ],
            ];
    
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($fcmUrl, $messageData);
    
            if ($response->successful()) {
                return 'FCM message sent successfully';
            } else {
                return 'Failed to send FCM message';
            }
    
            return response()->json(['message'=>"Success"]);
        }
        
       
        return response()->json(['message'=>"Failed"]);
        // Send the message to the specified device tokens
        
        

        // $fields = [
        //     'app_id' => $appID,
        //     'contents' => ['en' => 'Hello testings'],
        //     'included_segments' => 'All',
        // ];
        
        // $url = 'https://onesignal.com/api/v1/notifications';
        
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        // // Set the url, number of POST vars, POST data
        // curl_setopt($ch, CURLOPT_URL, $url);
        
        // // Set cURL options
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'Authorization: Basic ' . $onesignalUserAuth,
        //     'Content-Type: application/json',
        // ]);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // // Execute post
        // $result = curl_exec($ch);
        
        // // Get the HTTP status code before closing the cURL handle
        // $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // // Close connection
        // curl_close($ch);
        
        // // Check the response for success or error handling
        // if ($statusCode == 200) {
        //     return response()->json(['message' => 'Notification sent successfully']);
        // } else {
        //     return response()->json(['error' => 'Notification failed: ' . $result], $statusCode);
        // }
        

     }


    public function isNoti($id){

        
        // if($id==3){
        //     return response()->json(['title'=>'Alert','body'=>'You have a update']);
        // }
        // return response()->json(['title'=>'None']);
       
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
