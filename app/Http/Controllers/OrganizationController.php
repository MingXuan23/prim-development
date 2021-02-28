<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use View;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('organization.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('organization.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrganizationRequest $request)
    {

        //create organization
        $organization = Organization::create($request->validated());
        
        //attach foreign key to pivot table 
        $organization->user()->attach(Auth::id(), ['role_id' => 2]);

        $user = Auth::user();
        
        $user->assignRole('Admin');

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
            Session::flash('success', 'Organization Delete Successfully');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Organization Delete Failed');
            return View::make('layouts/flash-messages');
        }
    }

    public function getOrganizationDatatable()
    {
        $userId = Auth::id();

        // $organizationList = Organization::whereHas('user', function ($query) use ($userId) {
        //     $query->where('user_id', $userId);
        // })->get();

        $data = DB::table('organizations')
                ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
                ->join('users', 'users.id', '=', 'organization_user.user_id')
                ->select('organizations.id','organizations.nama', 'organizations.telno', 'organizations.email', 'organizations.address')
                ->where('users.id', $userId)
                ->orderBy('organizations.nama');

        if (request()->ajax()) {
            return datatables()->of($data)
                ->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('organization.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                })
                ->make(true);
        }
    }
}
