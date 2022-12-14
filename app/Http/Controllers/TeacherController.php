<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherExport;
use App\Exports\PerananExport;
use App\Imports\TeacherImport;
use App\Imports\WardenImport;
use App\Imports\GuardImport;
use App\Models\Organization;
use App\Models\OrganizationRole;
use App\User;
use Illuminate\Validation\Rule;
use App\Models\TypeOrganization;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

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

    //for warden and guard 
    public function perananindex()
    {
        $organization = $this->getOrganizationByUserId();

        $roles = DB::table('organization_roles')
            ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
            ->where([
                ['ou.user_id', Auth::user()->id],
            ])
            ->value('organization_roles.nama');

        return view('dorm.warden.index', compact('organization', 'roles'));
    }

    public function teacherexport(Request $request)
    {
        return Excel::download(new TeacherExport($request->organ), 'teacher.xlsx');
    }

    //for warden
    public function perananexport(Request $request)
    {
        return Excel::download(new PerananExport($request->organ), 'peranan.xlsx');
    }

    public function teacherimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {

            return redirect('/teacher')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        Excel::import(new TeacherImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/teacher')->with('success', 'Techers have been added successfully');
    }

    //for warden use
    public function wardenimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {

            return redirect('/teacher/peranan')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        Excel::import(new WardenImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/teacher/peranan')->with('success', 'Wardens have been added successfully');
    }

    //for guard use
    public function guardimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {

            return redirect('/teacher/peranan')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        Excel::import(new GuardImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/teacher/peranan')->with('success', 'Guards have been added successfully');
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        return view('teacher.add', compact('organization'));
    }

    //for warden and guard
    public function peranancreate()
    {
        $organization = $this->getOrganizationByUserId();


        return view('dorm.warden.add', compact('organization'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'          =>  'required',
            // 'icno'          =>  'required',
            'email'         =>  'required',
            'telno'         =>  'required',
            'organization'  =>  'required',
        ]);

        //check if parent role exists
        $ifExits = DB::table('users as u')
            ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
            ->where('ou.role_id', '=', '6')
            ->where('u.email', '=', "{$request->get('email')}")
            // ->where('u.icno', '=', "{$request->get('icno')}")
            ->where('u.telno', '=', "{$request->get('telno')}")
            ->get();

        // dd($ifExits);

        if (count($ifExits) == 0) // if not parent
        {
            $this->validate($request, [
                // 'icno'          =>  'required|unique:users',
                'email'         =>  'required|email|unique:users',
            ]);

            $newteacher = new Teacher([
                'name'           =>  $request->get('name'),
                // 'icno'           =>  $request->get('icno'),
                'email'          =>  $request->get('email'),
                'password'       =>  Hash::make('abc123'),
                'telno'          =>  $request->get('telno'),
                'remember_token' =>  $request->get('_token'),
                // 'created_at'     =>  now(),
            ]);
            $newteacher->save();
        } else // if parent
        {
            $newteacher = DB::table('users')
                ->where('email', '=', "{$request->get('email')}")
                ->first();
        }

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

    //for warden and guard
    public function perananstore(Request $request)
    {
        $this->validate($request, [
            'name'          =>  'required',
            'email'         =>  'required',
            'telno'         =>  'required',
            'organization'  =>  'required',
            'peranan'       =>  'required'
        ]);


        //check if parent role exists or teacher ?
        $ifExists = DB::table('users as u')
            ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
            ->whereIn('ou.role_id', [5, 6])
            ->where('u.email', '=', "{$request->get('email')}")
            ->where('u.telno', '=', "{$request->get('telno')}")
            ->get()
            ->count();

        //if the user is completely new
        if ($ifExists == 0) {
            $newteacher = new Teacher([
                'name'           =>  $request->get('name'),
                'email'          =>  $request->get('email'),
                'password'       =>  Hash::make('abc123'),
                'telno'          =>  $request->get('telno'),
                'remember_token' =>  $request->get('_token'),
                // 'created_at'     =>  now(),
            ]);
            $newteacher->save();
        } else {
            //get the teacher or parent id
            $newteacher = DB::table('users')
                ->where('users.email', '=', $request->get('email'))
                ->where('users.telno', '=', $request->get('telno'))
                ->first();
        }

        //if the user create warden
        if ($request->get('peranan') == 1) {
            $username    = DB::table('users')
                ->where('id', $newteacher->id)
                ->update(
                    [
                        'username' => 'GP' . str_pad($newteacher->id, 5, "0", STR_PAD_LEFT),
                    ]
                );

            // warden active when first time login then will change status
            DB::table('organization_user')->insert([
                'organization_id'   => $request->get('organization'),
                'user_id'           => $newteacher->id,
                'role_id'           => 7,
                'start_date'        => now(),
                'status'            => 0,
            ]);

            $user = User::find($newteacher->id);

            // role warden
            $rolename = OrganizationRole::find(7);
            $user->assignRole($rolename->nama);

            return redirect('/teacher/peranan')->with('success', 'New warden has been added successfully');
        }
        //else the user create guard
        else if ($request->get('peranan') == 2) {
            $username    = DB::table('users')
                ->where('id', $newteacher->id)
                ->update(
                    [
                        'username' => 'GS' . str_pad($newteacher->id, 5, "0", STR_PAD_LEFT),
                    ]
                );

            // warden active when first time login then will change status
            DB::table('organization_user')->insert([
                'organization_id'   => $request->get('organization'),
                'user_id'           => $newteacher->id,
                'role_id'           => 8,
                'start_date'        => now(),
                'status'            => 0,
            ]);

            $user = User::find($newteacher->id);

            // role guru
            $rolename = OrganizationRole::find(8);
            // dd($rolename);

            $user->assignRole($rolename->nama);

            return redirect('/teacher/peranan')->with('success', 'New guard has been added successfully');
        } else {
            dd("no peranan selected");
        }
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

    //for warden use
    public function perananedit($ou_id)
    {
        
        // $teacher = $this->teacher->getOrganizationByUserId($id);
        if(Auth::user())
        {
            $login_user = Auth::user();
            $role_id = DB::table('organization_user')
            ->where('user_id',$login_user->id)
            ->value('role_id');
            $organisation_id = DB::table('organization_user')
            ->where('user_id',$login_user->id)
            ->value('organization_id');
            $request_organization_id = DB::table('organization_user')
            ->where('id',$ou_id)
            ->value('organization_id');
            // parent role
            $parent_role_id = 6;
            // authorised role = superadmin, admin, pentadbir
            $superadmin = 1;
            $authorised_role = [2,4];
            // user only can view his own organisation 
            if($role_id != $superadmin)
            {
                if($organisation_id == $request_organization_id)
                {
                    if(!in_array($role_id,$authorised_role))
                    {
                        if($role_id != $ou_id)
                        {
                            return view('errors.404');
                        }
                    }
                }
                else{
                    return view('errors.404');
                }
            }
        }
        $teacher = DB::table('users')
            ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
            ->join('organizations', 'organization_user.organization_id', '=', 'organizations.id')
            ->where('organization_user.id', $ou_id)
            // ->where('organization_user.role_id', 7)
            ->select('organizations.id as organization_id', 'users.id as uid', 'users.name as tcname',  'users.email as email', 'users.telno as telno', 'organization_user.role_id as role_id')
            ->first();

        $id = DB::table('organization_user')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            // ->join('organizations', 'organization_user.organization_id', '=', 'organizations.id')
            ->where('organization_user.id', $ou_id)
            // ->where('organization_user.role_id', 7)
            ->value('users.id');

        // dd($ou_id, $id);

        $organization = $this->getOrganizationByUserId();

        // dd($teacher);
        return view('dorm.warden.update', compact('teacher', 'organization', 'id'));
    }

    public function update(Request $request, $id)
    {
        //
        $uid = User::find($id);
        // dd($id);

        $this->validate($request, [
            'name'          =>  'required',
            // 'icno'          =>  'required|unique:users,icno,' . $uid->id,
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
                    // 'icno'      => $request->get('icno'),
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

    //for warden use
    public function perananupdate(Request $request, $id)
    {
        //
        $uid = User::find($id);
        // dd($uid);

        $this->validate($request, [
            'name'          =>  'required',
            'email'         =>  'required|unique:users,email,' . $uid->id,
            'telno'         =>  'required',
        ]);

        $teacherupdate    = DB::table('users')
            ->where('users.id',  $uid->id)
            ->update(
                [
                    'name'      => $request->get('name'),
                    'email'     => $request->get('email'),
                    'telno'     => $request->get('telno'),
                    // 'icno'      => $request->get('icno'),
                ]
            );

        DB::table('organization_user')
            ->where('organization_user.user_id', $id)
            ->update(
                [
                    'organization_id'      => $request->get('organization'),
                ]
            );

        return redirect('/teacher/peranan')->with('success', 'The data has been updated!');
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

    //for warden and guard use
    public function peranandestroy($id)
    {

        $arrayPeranan = [7, 8];
        $result = DB::table('users')
            ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
            ->join('organizations', 'organization_user.organization_id', '=', 'organizations.id')
            ->where('users.id', $id)
            ->whereIn('organization_user.role_id', $arrayPeranan)
            ->update(
                [
                    'organization_user.status' => 0,
                ]
            );

        if ($result) {
            Session::flash('success', 'Peranan Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Peranan Gagal Dipadam');
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
            // dd($data);
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

    //for warden and guard use
    public function getPerananDatatable(Request $request)
    {
        if (request()->ajax()) {
            $oid = $request->oid;
            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $arrayPeranan = [7, 8];
                $data = DB::table('organizations')
                    ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
                    ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
                    ->join('users', 'users.id', '=', 'organization_user.user_id')
                    ->select('organizations.id as oid', 'organization_user.id as ou_id', 'organization_user.status as status', 'organization_roles.nama as roleName', 'users.id', 'users.name', 'users.email', 'users.username', 'users.icno', 'users.telno')
                    ->where('organizations.id', $oid)
                    ->whereIn('organization_user.role_id', $arrayPeranan)
                    ->orderBy('roleName');
            }

            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('teacher.perananedit', $row->ou_id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);

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
            //micole try
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->whereIn('role_id', [4, 5, 7, 8]);
            })->get();
        }
    }
}
