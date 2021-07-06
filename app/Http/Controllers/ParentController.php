<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationRole;
use App\Models\Parents;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ParentController extends Controller
{

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        //
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();
        return view('parent.index', compact('organization'));
    }

    public function indexDependent($id)
    {
        //
        // dd($id);
        $userId = $id;
        $organization = $this->getOrganizationByUserId();

        $role   = DB::table('organization_roles')
            ->where('id', '!=', 1)
            ->where('id', '!=', 2)
            ->where('id', '!=', 3)
            ->where('id', '!=', 4)
            ->where('id', '!=', 5)
            ->get();


        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.nama as nschool', 'students.nama as studentname', 'classes.nama as classname', 'organization_roles.nama as rolename')
            ->where([
                ['users.id', $userId],
            ])
            ->orderBy('students.nama')
            ->get();

        return view('parent.dependent.index', compact('list', 'role', 'organization', 'userId'));
    }

    public function fetchClass(Request $request)
    {

        // dd($request->get('schid'));
        $oid = $request->get('oid');

        $list = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.nama as nschool', 'classes.id as cid', 'classes.nama as cname')
            ->where('organizations.id', $oid)
            ->get();

        return response()->json(['success' => $list]);
    }

    public function fetchStd(Request $request)
    {

        // dd($request);
        $classid = $request->get('cid');

        $list = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->select('students.nama as namestd', 'students.id as sid')
            ->where('classes.id', $classid)
            ->get();

        return response()->json(['success' => $list]);
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        // $organization = DB::table('organizations')
        //     ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
        //     ->join('users', 'organization_user.user_id', '=', 'users.id')
        //     ->where('organization_user.user_id', Auth::id())
        //     ->Where(function ($query) {
        //         $query->where('organization_user.role_id', '=', 1)
        //             ->Orwhere('organization_user.role_id', '=', 2)
        //             ->Orwhere('organization_user.role_id', '=', 4);
        //     })
        //     ->select('organizations.id as id', 'organizations.nama as nama')
        //     ->distinct()
        //     ->get();

        return view('parent.add', compact('organization'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|unique:users',
            'email'         =>  'required|email|unique:users',
            'telno'         =>  'required',
        ]);

        $newparent = new Parents([
            'name'           =>  strtoupper($request->get('name')),
            'icno'           =>  $request->get('icno'),
            'email'          =>  $request->get('email'),
            'password'       =>  Hash::make('abc123'),
            'telno'          =>  $request->get('telno'),
            'remember_token' =>  Str::random(40),
            // 'created_at'     =>  now(),
        ]);
        $newparent->save();

        return redirect('/parent')->with('success', 'New parent has been added successfully');
    }

    public function storeDependent(Request $request)
    {
        //
        $this->validate($request, [
            'organization'  =>  'required',
            'classes'       =>  'required',
            'student'       =>  'required',
        ]);

        $userId = $request->get('parentid');
        $schid = $request->get('organization');
        $roles = $request->get('roles');

        $ouid = DB::table('organization_user')->insertGetId([
            'organization_id'   => $schid,
            'user_id'           => $userId,
            'role_id'           => $roles,
            'start_date'        => now(),
            'status'            => 1,
        ]);

        // dd($schid);
        $stdid = $request->get('student');

        $list = DB::table('organization_user_student')
            ->insert([
                'organization_user_id'  => $ouid,
                'student_id'            => $stdid

            ]);

        // $user = User::find($userId);

        $user = $this->user->getUser($userId);
        // $role = Role::create(['name' => 'parent']);

        $rolename = OrganizationRole::find($roles);

        $user->assignRole($rolename->nama);
        return redirect('dependent/'.$userId)->with('success', 'New dependents has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role guru 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5);
                });
            })->get();
        }
    }

    public function getParentDatatable(Request $request)
    {
        // dd($request->icno);


        if (request()->ajax()) {

            $userId = Auth::id();
            // $data = Parents::where('icno', $request->icno)->first();
            $data = DB::table('users')
                ->where('icno', $request->icno)
                ->get();

            // dd($data);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('parent.dependent', $row->id) . '" class="btn btn-success m-1"> <span class="fa fa-search"></span></a></div>';
                // $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }
}
