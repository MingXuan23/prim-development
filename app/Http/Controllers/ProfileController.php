<?php

namespace App\Http\Controllers;
// namespace App\Http\Requests; // can delete soon
// use App\Http\Requests\Controller; // can delete soon
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // new added
use Illuminate\Support\Facades\Hash;
use App\Http\Jajahan\Jajahan;

class ProfileController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$unseenProfile = false;
        $userData =  Auth::user(); // get all data of a certain user with particular ID
        // return view('users.index', compact('userData', 'unseenProfile'));
        return view('users.index', compact('userData'));
        
        // Get the currently authenticated user...
        //$user = Auth::user();
        // must include library: use Illuminate\Support\Facades\Auth;
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        // $unseenProfile = false;
        // return view('users.edit', compact('unseenProfile')); 
        $states = Jajahan::negeri();
        return view('users.edit',compact('states')); 
        // return the data in edit mode.
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $id = Auth::id(); 
        // to prepare the data before validation
        // $request->merge([
        //     'telno' => str_replace( '+6', '', $request->post('telno')),
        // ]);

        $request->validate([
            'name'      => 'required',
            'telno'     => "required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,telno,$id",
            'email'     => "required|email|unique:users,email,$id",
        ]);

        // unique: tableName, columName, $id to exclude

        $userUpdate = DB::table('users')
            ->where('id', $id)
            ->update(
                [
                    'name'      => $request->post('name'),
                    'email'     => $request->post('email'),
                    'username'  => $request->post('username'),
                    'telno'     => $request->post('telno'),
                    'address'   => $request->post('address'),
                    'state'     => $request->post('state'),
                    'postcode'  => $request->post('postcode')
                ]
            );
        return redirect()->route('profile_user')->with('success','Profil berjaya dikemaskini');   
    }

    // not using destroy
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     //
    // }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

      /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        // button to save
        // havent test
        //return redirect()->route('users.index')->with('success', 'Profile updated!'); // just example; need to add status and message for user
    }

    public function showChangePwd(){
       // $userData =  Auth::user(); // get all data of a certain user with particular ID
        // return view('users.resetPwd', compact('userData'));
        return view('users.resetPwd');
    }

    public function updatePwd(Request $request, User $user){
        $hashedPassword = Auth::user()->password;
        $id = Auth::id();
        
        $request->validate([
            'old_password'              => ['required', 'min:8'],
            'password'                  => ['required', 'confirmed', 'min:8','regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[@!$#%^&*()]).*$/'],
            'password_confirmation'     => ['required',' same:password']
        ],
        [
            'password.regex' => 'Password must contains at least 1 number, 1 uppercase, 1 special character (@!$#%^&*())',
            'password.min' => 'Kata laluan mesti lebih daripada 8'
        ]);

        // check if the password is match
        if (Hash::check($request->old_password, $hashedPassword)){
            // check if new password is same with old password
            if (Hash::check($request->password , $hashedPassword)){

            
                return redirect()->back()->with('error', 'Old and new password cannot be the same');

            } else{
                $userUpdate = DB::table('users')
                ->where('id', $id)
                ->update(
                    [
                        // update into db
                        'password'   => Hash::make($request->password)
                    ]
                );
                return redirect()->route('profile_user')->with('success','Password Updated');   

            }
        } else{
        //     // password not match between user entered and actual current password
            return redirect()->back()->with('error', 'Old password does not match');
        }
    }
   
}
