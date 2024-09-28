<?php

namespace App\Http\Controllers;

use App\code_request;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use stdClass;

use Yajra\DataTables\DataTables;



class CodeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $languages = DB::table('code_language')->where('status',1)->get();

        $packages =  $package = DB::table('code_package')
                        ->where('status',1)
                        ->orderBy('price')
                        ->get();
        return view('code_request.index',compact('languages','packages'));
    }

    public function validateRequest(Request $request){
       // dd($request);
       if ($request->ajax()) {

            // Define validation rules
            $validator = Validator::make($request->all(), [
                'name'  => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
                'telno' => ['required', 'numeric', 'min:10'],
                'problem_description' =>['required']
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                // Return the first validation error messages in JSON format
                return response()->json([
                    'status'  => 'error',
                    'message' => $validator->errors()->first()  // Get first error message
                ]); // HTTP status 422 Unprocessable Entity
            }

            // Count the number of statements in the source code
            $line = $this->countStatements($request->source_code);

            if($line == 0){
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Enter your source code'  // Get first error message
                ]);
            }

           
            $languages = DB::table('code_language')->where('id',$request->language)->first();

            if($request->type == "A"){
                $package = DB::table('code_package')
                            ->where('type','A')
                            ->where('num_line','>=',$line)
                            ->orderBy('num_line')
                            ->first();
            }else if($request->type == "B"){
                $package = DB::table('code_package')
                ->where('type','B')
                ->where('id','>=',$request->package)
                ->first();
            }

            if(!isset($languages) || !isset($package)){
                return response()->json([
                    'status'  => 'error',
                    'message' => 'We cannot process your request.Please try again. Error Code:100' 
                ]);
            }
            
            $price = $package->price * $languages->price_weight;
            $time_now = now();

            $draft = DB::table('code_requests')
            ->where('email',$request->email)
            ->where('status','Draft')
            ->first();
           if(isset($request->session_id) || isset($draft)){
                
                $session = explode('-', $request->session_id);
               // dd($session);
                $date_string = isset($session[1])?Carbon::createFromFormat('YmdHis', $session[1]):$draft->updated_at;
                $id = (isset($session[0])&& $session[0]!="")? $session[0]:$draft->id;
            //dd($id);
                
                $exist = DB::table('code_requests')
                ->where('id',$id)
                ->where('updated_at',$date_string)
                ->where('status','Draft')
                ->update([
                    'name' =>$request->name,
                    'phone' =>$request->telno,
                    'language_id' =>$languages->id,
                    'package_id' =>$package ->id,
                    'final_price' => $price,
                    'source_code' => $request->source_code,
                    'problem_description' =>$request->problem_description,
                    'updated_at' =>$time_now,

                ]);

                if(!$exist){
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'We cannot process your request.Please try again. Error Code:200' 
                    ]);
                }
           }else{

            $id = DB::table('code_requests')
                
            ->insertGetId([
                'email' => $request->email,
                'status' => 'Draft',
                'name' =>$request->name,
                'phone' =>$request->telno,
                'language_id' =>$languages->id,
                'package_id' =>$package ->id,
                'final_price' => $price,
                'source_code' => $request->source_code,
                'problem_description' =>$request->problem_description,
                'created_at' =>$time_now,
                'updated_at' =>$time_now,

            ]);

           }
           

            
           
            return response()->json([
                'status'           => 'success',
                'num_of_statement' => $line,
                'price' =>$price,
                'session_id' =>  $id.'-'.$time_now->format('YmdHis')
            ]);
        }
        
        
    }


    // public function track_list(){
    //    // dd("here");
       
    //     return view('code_request.list-user');
    // }

    public function list_by_email(Request $request){
        if(request()->ajax()){
            $email = $request->email;

            $data = DB::table('code_requests as cr')
            ->join('code_language as cl','cl.id','cr.language_id')
            ->join('code_package as cp','cp.id','cr.package_id')
            //->join()
                ->where('cr.email',$email)
                ->whereIn('cr.status',['Pending Helper','Helper Helping','Completed','Payment Failed'])
                
                ->select('cr.id','cr.name','cr.email','cr.phone','cl.name as language','cp.name as package','cr.final_price','cr.created_at','cr.status')
                ->get();

            $table = Datatables::of($data);
            $table->addColumn('status', function ($row) {
                if($row->status == 'Helper Helping'){
                    $helpers = json_decode($row->helpers);
                    return '<span class="badge badge-info">'.count($helpers).' ' . $row->status . '</span>';


                }else if($row->status == 'Completed'){
                    return '<span class="badge badge-success">' . $row->status . '</span>';

                }else if($row->status == 'Payment Failed'){
                    return '<span class="badge badge-danger">' . $row->status . '</span>';

                }
                return '<span class="badge badge-warning">' . $row->status . '</span>';
            });
    
            // Add the 'action' column with the receipt button
            $table->addColumn('action', function ($row) {
                $token = $row->id . '-' . Carbon::parse($row->created_at)->format('YmdHis');
                if($row->status != 'Payment Failed'){
                    return '<a href="' . route('codereq.receipt', $token) . '" class="btn btn-primary">Receipt</a>';

                }

                return 'Failed';
            });
    
            // Allow HTML rendering in the returned DataTable
            return $table->rawColumns(['status', 'action'])->make(true);
        }
    }

    public function show_source_code($id){
        if(!auth()->user()->hasRole('Helper')){
            return view('errors.404');
        }

        $req = DB::table('code_requests')
        ->where('id', $id)
        ->select('problem_description','source_code')
        ->first();

        //dd($req);
       

        return view('code_request.source_code',compact('req'));

    }
    public function helperList(){
        if(!auth()->user()->hasRole('Helper')){
            return view('errors.404');
        }

        $status_category = DB::table('code_requests')
                            ->where('status','<>','Draft')
                            ->select('status')
                            ->groupBy('status')
                            ->orderBy('status','desc')
                            ->pluck('status')
                            
                            ->toArray();
        if (($key = array_search('Pending Helping', $status_category)) !== false) {
            // Remove it from the current position
            unset($status_category[$key]);
            // Add it to the beginning of the array
            array_unshift($status_category, 'Pending Helping');
        }
        return view('code_request.list-helper',compact('status_category'));
    }


    public function helper_join_request($id, $status) {
        if (!auth()->user()->hasRole('Helper')) {
            return redirect()->back()->with('error', 'Error');
        }
    
        // Retrieve the request from the database
        $request = DB::table('code_requests')->where('id', $id)->first();
    
        // Initialize an empty array for helpers
        $helpers = [];
    
        // Check if the request has any helpers already
        if ($request->helpers != null && $status != 'Completed') {
            // Decode the existing helpers JSON into an array of objects
            $helpers = json_decode($request->helpers);
            
            // If the helper ID already exists, return early
            if ($this->check_helper_request(Auth::id(), $request->id)) {
                return redirect()->back()->with('message', 'You helped this request already');
            }
        }
    
        $update = false;
    
        if ($status == 'Helping') {
            $helper = new stdClass();
            $helper->helper_id = Auth::id();  // Assign the current authenticated user's ID
            $helper->status = $status;        // Set the helper's status
            $helper->datetime = now()->format('Y-m-d H:i'); // Set the current timestamp
    
            // Add the helper to the helpers array
            $helpers[] = $helper;
    
            // Update the helpers column in the database with the new helpers array (encoded as JSON)
            $update = DB::table('code_requests')
                ->where('id', $id)
                ->update(['helpers' => json_encode($helpers), 'status' => 'Helper Helping']);
        } else if ($status == 'Completed') {
            $helpers = json_decode($request->helpers);

            foreach ($helpers as $helper) {
                if ($helper->helper_id == Auth::id()) {
                    $helper->status = 'Completed';
                    $helper->complete_time = now()->format('Y-m-d H:i'); // Update the datetime as well
                    
                    // Save the updated helpers array back to the database
                    $update = DB::table('code_requests')
                        ->where('id', $id)
                        ->update(['helpers' => json_encode($helpers), 'status' => 'Completed']);
                    break; // Break after updating to avoid multiple updates
                }
            }
        }
    
        if ($update) {
            return redirect()->back()->with('message' , 'The request status is ' . $status . ' now');
        }
    
        return redirect()->back()->with('error', 'Error');
    }
    
    
    public function check_helper_request($helper_id,$request_id){

        $request = DB::table('code_requests')
                    ->where('id', $request_id)
                    ->first();

        if(!isset($request->helpers)){
           // dd($request);

            return false;
        }
        //dd($request);


        $helpers = json_decode($request->helpers);
       
        // Use array_filter to check if the current helper ID already exists
        $existingHelper = array_filter($helpers, function($h) use ($helper_id) {
            //dd($h->helper_id);
            return $h->helper_id == $helper_id;
        });

        // If the helper ID already exists, return true
        return !empty($existingHelper);
        
    }
   
    public function list_by_helper(Request $request){
        if(auth()->user()->hasRole('Helper') && request()->ajax()){
            $data = DB::table('code_requests as cr')
            ->join('code_language as cl','cl.id','cr.language_id')
            ->join('code_package as cp','cp.id','cr.package_id')
            ->where('cr.status','<>','Draft')
            ->where('cr.status',$request->status)
            ->select('cr.id','cr.name','cr.email','cr.phone','cl.name as language','cp.name as package','cr.final_price','cr.created_at','cr.status','cr.helpers')
            ->get();

            $table = Datatables::of($data);
            $table->addColumn('profile', function ($row) {
                return $row->name ."<br>".$row->email."<br>".$row->phone;
            });
            $table->addColumn('status', function ($row) {
                if($row->status == 'Helper Helping'){
                    $helpers = json_decode($row->helpers);
                    return '<span class="badge badge-info">'.count($helpers).' ' . $row->status . '</span>';


                }else if($row->status == 'Completed'){
                    return '<span class="badge badge-success">' . $row->status . '</span>';

                }else if($row->status == 'Payment Failed'){
                    return '<span class="badge badge-danger">' . $row->status . '</span>';

                }
                return '<span class="badge badge-warning">' . $row->status . '</span>';
            });
    
            // Add the 'action' column with the receipt button
            $table->addColumn('action', function ($row) {
                if($row->status == 'Payment Failed'){
                    return '';
                }
                $token = $row->id . '-' . Carbon::parse($row->created_at)->format('YmdHis');
                $btns = '<a href="' . route('codereq.receipt', $token) . '" target="_blank" class="btn btn-primary mr-2">Receipt</a>';
              
                if ($row->status != 'Completed'){
                    if ($this->check_helper_request(Auth::id(), $row->id)) {
                        $btns .= '<a href="' . route('codereq.helper_join_request', ['id' => $row->id, 'status' => 'Completed']) . '" class="btn btn-success mr-2">Complete</a>';
                    } else {
                        $btns .= '<a href="' . route('codereq.helper_join_request', ['id' => $row->id, 'status' => 'Helping']) . '" class="btn btn-warning mr-2">Help</a>';
                    }
                }
                
            
                $helper = Auth::user();
                $msg = 'Hi, I am ' . $helper->name . ' from S Helper';
                
                // Remove '+' from the phone number
                $phone = str_replace('+', '', $row->phone);
                // Construct the WhatsApp URL
                $url = "https://api.whatsapp.com/send?phone=" . urlencode($phone) . "&text=" . urlencode($msg);
                
                $btns .= '<a href="' . $url . '" target="_blank" class="btn btn-info mr-2">Contact</a>';

                $btns .= '<a href="' . route('codereq.show_source_code', $row->id) . '" target="_blank" class="btn btn-dark">Code</a>';
            
                // Wrap all buttons in a div with d-flex and gap classes
                return '<div class="d-flex gap-2">' . $btns . '</div>';
            });
    
            // Allow HTML rendering in the returned DataTable
            return $table->rawColumns(['status', 'action','profile'])->make(true);
        }

        return null;
    }

    public function receipt($token){
        $session = explode('-', $token);
        $date_string = Carbon::createFromFormat('YmdHis', $session[1]);
        $id = $session[0];

        
        $codeRequest = DB::table('code_requests as cr')
                    ->join('transactions as t','t.id','cr.transaction_id')
                    ->where('cr.id',$id)
                    ->where('cr.created_at',$date_string)
                    ->where('t.status','Success')
                    ->select('cr.*')
                    ->first();
        
        $details = DB::table('code_requests as cr')
                ->join('code_language as cl','cl.id','cr.language_id')
                ->join('code_package as cp','cp.id','cr.package_id')
                ->join('transactions as t','t.id','cr.transaction_id')
                ->where('cr.id',$codeRequest->id)
                ->select('cl.name as language_name','cp.name as package_name','t.transac_no')
                ->first();

        return view('code_request.receipt',compact('codeRequest','details'));

    }

    public function countStatements($code)
    {
        // Remove any leading or trailing whitespace
        $code = trim($code);

        //remove the content inside /**/ 
        $code = preg_replace('/\/\*.*?\*\//s', '', $code); 
        // If the code is empty, return 0
        if (empty($code)) {
            return 0;
        }

        // Replace newline characters with a single space to handle multiline statements
        $code = preg_replace('/\R+/', ';', $code);
        //$statements = preg_split('/\R+/', $code, -1, PREG_SPLIT_NO_EMPTY);
        // Split the code by semicolons to count statements
        $statements = preg_split('/;\s*/', $code, -1, PREG_SPLIT_NO_EMPTY);
        //dd($statements);
        // Remove comments starting with //
        $statements = array_filter($statements, function ($statement) {
            return !preg_match('/^\s*\/\//', trim($statement)); 
        });
        // Count the number of non-empty statements

        return count($statements);
        
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
        if(!isset($request->session_id)){
           
            return redirect()->back()->with('error', "Your request was failed to process");
        }

        $session = explode('-', $request->session_id);
        $date_string = Carbon::createFromFormat('YmdHis', $session[1]);
        $id = $session[0];

        $draft = DB::table('code_requests')->where('id',$id)->where('updated_at',$date_string)->where('status','Draft')->first();

        if(!isset($draft) || count($session)>2 ){
           // dd('here2');

            return redirect()->back()->with('error', "Your request was failed to process");
        }
        

        $input_validation = $request->email == $draft->email && $request->telno==$draft->phone&& 
                           $request->source_code == $draft->source_code &&
                            $request->price == $draft->final_price;
        
        if(!$input_validation){
           
            return redirect()->back()->with('error', "Your request was failed to process");
        }           
        
        $amount=$draft->final_price;
        $request_id = $draft->id;
        return view('code_request.payment',compact('amount','request_id'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\code_request  $code_request
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
        return view('code_request.list-user');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\code_request  $code_request
     * @return \Illuminate\Http\Response
     */
    public function edit(code_request $code_request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\code_request  $code_request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, code_request $code_request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\code_request  $code_request
     * @return \Illuminate\Http\Response
     */
    public function destroy(code_request $code_request)
    {
        //
    }
}
