<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherExport;
use App\Imports\TeacherImport;
use App\Models\Organization;
use App\Models\OrganizationRole;
use App\User;
use Illuminate\Validation\Rule;
use App\Models\TypeOrganization;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class TeacherController extends Controller
{

    private $teacher;
    public function __construct(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function index()
    {
        $organization = $this->getOrganizationByUserId();
        return view('teacher.index', compact('organization'));
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
        $organization = $this->getOrganizationByUserId();

        return view('teacher.add', compact('organization'));
    }

    public function store(Request $request)
    {
        //

        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|unique:users',
            'email'         =>  'required|email|unique:users',
            'telno'         =>  'required',
            'organization'  =>  'required',
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

        // teacher active when first time login then will change status
        DB::table('organization_user')->insert([
            'organization_id'   => $request->get('organization'),
            'user_id'           => $newteacher->id,
            'role_id'           => 5,
            'start_date'        => now(),
            'status'            => 0,
        ]);

        $user = User::find($newteacher->id);

        // role guru
        $rolename = OrganizationRole::find(5);
        $user->assignRole($rolename->nama);


        return redirect('/teacher')->with('success', 'New teacher has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        // $teacher = $this->teacher->getOrganizationByUserId($id);

        $teacher = DB::table('users')
            ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
            ->join('organizations', 'organization_user.organization_id', '=', 'organizations.id')
            ->where('users.id', $id)
            ->where('organization_user.role_id', 5)
            ->select('organizations.id as organization_id', 'users.id as uid', 'users.name as tcname', 'users.icno as icno', 'users.email as email', 'users.telno as telno', 'organization_user.role_id as role_id')
            ->first();

        $organization = $this->getOrganizationByUserId();

        // dd($teacher);
        return view('teacher.update', compact('teacher', 'organization'));
    }

    public function update(Request $request, $id)
    {
        //
        $uid = User::find($id);
        // dd($id);

        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|unique:users,icno,' . $uid->id,
            'email'         =>  'required|unique:users,email,' . $uid->id,
            'telno'         =>  'required',
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

        DB::table('organization_user')
            ->where('user_id', $id)
            ->update(
                [
                    'organization_id'      => $request->get('organization'),
                ]
            );

        return redirect('/teacher')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {

        $result = DB::table('users')
            ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
            ->join('organizations', 'organization_user.organization_id', '=', 'organizations.id')
            ->where('users.id', $id)
            ->where('organization_user.role_id', 5)
            ->update(
                [
                    'organization_user.status' => 0,
                ]
            );


        if ($result) {
            Session::flash('success', 'Guru Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Guru Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }


    public function getTeacherDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $oid = $request->oid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('organizations')
                    ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
                    ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
                    ->join('users', 'users.id', '=', 'organization_user.user_id')
                    ->select('organizations.id as oid', 'organization_user.status as status', 'users.id', 'users.name', 'users.email', 'users.username', 'users.icno', 'users.telno')
                    ->where('organizations.id', $oid)
                    ->where('organization_user.role_id', 5)
                    ->orderBy('users.name');
            }
            // elseif ($hasOrganizaton == "true") {
            //     $data = DB::table('organizations')
            //         ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            //         ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
            //         ->join('users', 'users.id', '=', 'organization_user.user_id')
            //         ->select('organizations.id as oid', 'organization_user.status as status', 'users.id', 'users.name', 'users.email', 'users.username', 'users.icno', 'users.telno')
            //         ->where('organization_user.role_id', 5)
            //         ->where('users.id', Auth::id())
            //         ->orderBy('users.name');
            // }

            // dd($data->oid);
            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if ($row->status == '1') {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-success">Aktif</span></div>';

                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Tidak Aktif </span></div>';

                    return $btn;
                }
            });

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('teacher.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['status', 'action']);
            return $table->make(true);
        }
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role pentadbir 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('role_id', 4);
            })->get();
        }
    }
}
