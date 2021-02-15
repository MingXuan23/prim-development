<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $userId = Auth::id();

        $listorg = Organization::whereHas('user', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return view('organization.index', compact('listorg'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('organization.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'         =>  'required',
            // 'code'         =>  'required',
            'telno'        =>  'required|numeric',
            'email'        =>  'required',
            'address'      =>  'required',
            'postcode'     =>  'required',
            'state'        =>  'required',
        ]);

        $organization = Organization::create([
            'nama'         =>  $request->get('name'),
            'telno'        =>  $request->get('telno'),
            'email'        =>  $request->get('email'),
            'address'      =>  $request->get('address'),
            'postcode'     =>  $request->get('postcode'),
            'state'        =>  $request->get('state'),
        ]);

        $organization->user()->attach(Auth::id(), ['role_id' => 2]);

        return redirect('/organization')->with('success', 'New organization has been added successfully');
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
        $org = DB::table('organizations')->where('id', $id)->first();

        //$userinfo = User_info::find($id);
        //dd($userinfo);
        return view('organization.update', compact('org'));
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
        $this->validate($request, [
            'name'         =>  'required',
            // 'code'         =>  'required',
            'telno'        =>  'required|numeric',
            'email'        =>  'required',
            'address'      =>  'required',
            'postcode'     =>  'required',
            'state'        =>  'required',
        ]);

        $orgupdate    = DB::table('organizations')
            ->where('id', $id)
            ->update(
                [
                    'nama'      => $request->get('name'),
                    // 'code'      => $request->get('code'),
                    'email'     => $request->get('email'),
                    'telno'     => $request->get('telno'),
                    'address'   => $request->get('address'),
                    'state'     => $request->get('state'),
                    'postcode'  => $request->get('postcode')
                ]
            );

        return redirect('/organization')->with('success', 'The data has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = Organization::find($id)->delete();

        if ($result) {
            return response()->json([
                'status' => 'success',
                'msg' => 'Organization Delete Successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'msg' => 'Organization Delete Failed'
            ]);
        }
    }
}
