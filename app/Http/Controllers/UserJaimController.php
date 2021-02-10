<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserJaimController extends Controller
{
    public function index()
    {
        //
        // User::assignRole('jaim');
        // $user = Auth::user();
        // $role = Role::create(['name' => 'super-admin']);
        // $role2 = Role::create(['name' => 'admin']);
        // // dd($user);
        // $user->assignRole('super-admin');  
        // auth()->user()->assignRole('jaim');
        // $user->assignRole('writer');
        return view('jaim.user.index');
    }

    public function create()
    {
        //
        return view('jaim.user.add');
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|numeric|unique:users',
            'email'         =>  'required|email|unique:users',
            'telno'         =>  'required|numeric',
        ]);

    }

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
