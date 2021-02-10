<?php

namespace App\Http\Controllers;

use App\models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherExport;
use App\Imports\TeacherImport;
use App\User;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{

    public function index()
    {
        //
        $userid     = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        // sekolah id
        $listteacher = DB::table('users')
            ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
            ->select('users.id as id', 'users.name', 'users.email', 'users.username', 'users.icno', 'users.telno', 'organization_user.status')
            ->where([
                ['organization_user.organization_id', $school->schoolid],
                ['organization_user.role_id', 2]
            ])
            ->orderBy('users.name')
            ->get();

        // dd($listteacher);
        return view("pentadbir.teacher.index", compact('listteacher'));
    }

    public function teacherexport()
    {
        return Excel::download(new TeacherExport, 'teacher.xlsx');
    }

    public function teacherimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        Excel::import(new TeacherImport, public_path('/uploads/excel/' . $namaFile));

        return redirect('/teacher')->with('success', 'New class has been added successfully');

    }

    public function create()
    {
        //
        return view('pentadbir.teacher.add');
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

        $newteacher = new Teacher([
            'name'           =>  $request->get('name'),
            'icno'           =>  $request->get('icno'),
            'email'          =>  $request->get('email'),
            'password'       =>  Hash::make('abc123'),
            'telno'          =>  $request->get('telno'),
            'remember_token' =>  $request->get('_token'),
            // 'created_at'     =>  now(),
        ]);
        $newteacher->save();

        $username    = DB::table('users')
            ->where('id', $newteacher->id)
            ->update(
                [
                    'username' => 'GP' . str_pad($newteacher->id, 5, "0", STR_PAD_LEFT),
                ]
            );

        $userid     = Auth::id();

        // amik sekolah id untuk guru
        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        DB::table('organization_user')->insert([
            'organization_id' => $list->schoolid,
            'user_id'       => $newteacher->id,
            'role_id'       => 2,
            'start_date'    => now(),
            'status'        => 0,
        ]);

        return redirect('/teacher')->with('success', 'New teacher has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $teacher = DB::table('users')->where('id', $id)->first();

        //$userinfo = User_info::find($id);
        // dd($teacher);
        return view('pentadbir.teacher.update', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        //
        $uid = User::find($id);

        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|numeric|unique:users,icno,' . $uid->id,
            'email'         =>  'required|email|unique:users,email,' . $uid->id,
            'telno'         =>  'required|numeric',
        ]);

        $teacherupdate    = DB::table('users')
            ->where('id', $id)
            ->update(
                [
                    'name'      => $request->get('name'),
                    'email'     => $request->get('email'),
                    'telno'     => $request->get('telno'),
                    'icno'      => $request->get('icno'),
                ]
            );

        return redirect('/teacher')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        //
        // $uid = User::findOrFail($id);
        // $uid->delete();

        // delete kat table orga user
        // return redirect('/teacher');
    }
}
