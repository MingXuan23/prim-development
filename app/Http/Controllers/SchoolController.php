<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index()
    {
        //
        // return view("pentadbir.school.index");
        $school = Organization::all()->toArray();
        return view('pentadbir.school.index', compact('school'));
    }

    public function create()
    {
        //
        return view('pentadbir.school.add');
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'         =>  'required',
            'code'         =>  'required',
            'telno'        =>  'required|numeric',
            'email'        =>  'required',
            'address'      =>  'required',
            'postcode'     =>  'required',
            'state'        =>  'required',
        ]);

        $school = new Organization([
            'nama'         =>  $request->get('name'),
            'code'         =>  $request->get('code'),
            'telno'        =>  $request->get('telno'),
            'email'        =>  $request->get('email'),
            'address'      =>  $request->get('address'),
            'postcode'     =>  $request->get('postcode'),
            'state'        =>  $request->get('state'),
        ]);

        $school->save();
        return redirect('/school')->with('success', 'New school has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $school = DB::table('organizations')->where('id', $id)->first();

        //$userinfo = User_info::find($id);
        //dd($userinfo);
        return view('pentadbir.school.update', compact('school'));
    }

    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name'         =>  'required',
            'code'         =>  'required',
            'telno'        =>  'required|numeric',
            'email'        =>  'required',
            'address'      =>  'required',
            'postcode'     =>  'required',
            'state'        =>  'required',
        ]);

        $sekolahupdate    = DB::table('organizations')
            ->where('id', $id)
            ->update(
                [
                    'nama'      => $request->get('name'),
                    'code'      => $request->get('code'),
                    'email'     => $request->get('email'),
                    'telno'     => $request->get('telno'),
                    'address'   => $request->get('address'),
                    'state'     => $request->get('state'),
                    'postcode'  => $request->get('postcode')
                ]
            );

        return redirect('/school')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        //
    }
}
