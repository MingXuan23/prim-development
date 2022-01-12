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
        return view('users.edit'); 
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

        $request->validate([
            'name'      => 'required',
            'telno'     => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/',
            'email'     => "required|email|unique:users,email,$id",
        ]);

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
        return redirect()->route('profile_user')->with('success','Profile updated successfully');   
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
   
}
