<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ParentsImport;
use App\Models\Organization;
use App\Models\OrganizationRole;
use App\Models\Parents;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
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

    public function indexSwasta()
    {
        //
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();
        return view('private-school.parent.index', compact('organization'));
    }

    public function indexDependent()
    {
        $userId =  Auth::id();
        
        $organization = $this->getOrganizationByUserId();

        return view('parent.dependent.index', compact('organization', 'userId'));
    }

    public function getDependentDataTable()
    {
        $userId =  Auth::id();

        $list = DB::table('organizations')
                ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
                ->join('users', 'users.id', '=', 'organization_user.user_id')
                ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
                ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
                ->join('students', 'students.id', '=', 'organization_user_student.student_id')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('organization_user_student.id as ousid', 'organizations.nama as sekolah', 'students.nama as nama', 'classes.nama as kelas', 'organization_roles.nama as rolename')
                ->where([
                    ['users.id', $userId],
                ])
                ->orderBy('classes.nama')
                ->get();

        if(request()->ajax())
        {
            return datatables()->of($list)
                ->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<button id="' . $row->ousid . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                })
                ->make(true);
        }
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
            ->where('classes.status', 1)
            ->orderBy('classes.nama')
            ->get();

        return response()->json(['success' => $list]);
    }

    public function fetchStd(Request $request)
    {

        // dd($request);
        $classid = $request->get('cid');

        // $list = DB::table('classes')
        //     ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
        //     ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
        //     ->join('students', 'students.id', '=', 'class_student.student_id')
        //     ->select('students.nama as namestd', 'students.id as sid')
        //     ->where('classes.id', $classid)
        //     ->get();
        $list = DB::table('students as s')
                ->leftJoin('class_student as cs', 's.id', '=', 'cs.student_id')
                ->select('s.nama as namestd', 's.id as sid')
                ->where('cs.organclass_id', '=', "{$classid}")
                ->whereNull('s.parent_tel')
                ->orderBy('s.nama')
                ->get();

        return response()->json(['success' => $list]);
    }

    public function create()
    {
        $user_id = Auth::id();

        $organization = DB::table('organizations as o')
                        ->leftJoin('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                        ->where('ou.user_id', '=', "{$user_id}")
                        ->select('o.id')
                        ->first();
        // dd($organization);
        return view('parent.add', compact('organization'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'oid'           =>  'required',
            'name'          =>  'required',
            'icno'          =>  'required',
            'email'         =>  'required',
            'telno'         =>  'required',
        ]);

        // check if teacher role exists
        $ifExits = DB::table('users as u')
                    ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
                    ->whereIn('ou.role_id', [5, 21])
                    ->where('u.email', '=', "{$request->get('email')}")
                    ->where('u.icno', '=', "{$request->get('icno')}")
                    ->where('u.telno', '=', "{$request->get('telno')}")
                    ->get();
        
        if(count($ifExits) == 0) // if not teacher
        {
            $this->validate($request, [
                'icno'          =>  'required|unique:users',
                'email'         =>  'required|email|unique:users',
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
        }
        else // add parent role
        {
            $newparent = DB::table('users')
                        ->where('email', '=', "{$request->get('email')}")
                        ->first();
        }

        DB::table('organization_user')->insert([
            'organization_id'   => $request->get('oid'),
            'user_id'           => $newparent->id,
            'role_id'           => 6,
            'start_date'        => now(),
            'status'            => 1,
        ]);

        $user = User::find($newparent->id);

        // role parent
        $rolename = OrganizationRole::find(6);
        $user->assignRole($rolename->nama);

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

        $userId = Auth::id();
        $user = DB::table('users')
                    ->where('id', "{$userId}")
                    ->first();
        $user_tel = $user->telno;
        $schid = $request->get('organization');
        $stdid = $request->get('student');

        $check_parent = DB::table('organization_user')
            ->where('organization_id', $schid)
            ->where('user_id', $userId)
            ->where('role_id', 6)
            ->where('status', 1)
            ->first();

        // dd($check_parent);
        if ($check_parent) {
            $ouid = $check_parent->id;
        } else {
            DB::table('organization_user')->insert([
                'organization_id'   => $schid,
                'user_id'           => $userId,
                'role_id'           => 6,
                'start_date'        => now(),
                'status'            => 1,
            ]);

            $ou = DB::table('organization_user')
                    ->where('user_id', "{$userId}")
                    ->where('organization_id', "{$schid}")
                    ->where('role_id', 6)
                    ->first();
            $ouid = $ou->id;
        }

        // $check_std = DB::table('organization_user_student')
        //     ->where('organization_user_id', $ouid)
        //     ->where('student_id', $stdid)
        //     ->first();

        // if ($check_std) {
        //     return redirect('/parent/dependent')->withErrors('Tanggungan ini telahpun ditambah');
        // } else {
            
        // }
        DB::table('organization_user_student')
                ->insert([
                    'organization_user_id'  => $ouid,
                    'student_id'            => $stdid
                ]);
            
        DB::table('students')
            ->where('id', $stdid)
            ->update(['parent_tel' => "{$user_tel}"]);

        return redirect('/parent/dependent/')->with('success', 'Tanggungan telah berjaya ditambah');
    }

    public function deleteDependent($id)
    {
        // dd($id);
        $user = DB::table('students as s')
                    ->leftJoin('organization_user_student as ous', 's.id', "=", 'ous.student_id')
                    ->select('s.id as sid')
                    ->where('ous.id', '=', $id)
                    ->first();
        // dd($user->sid);
        $result = DB::table('organization_user_student')->where('id', '=', $id)->delete();

        $ifSuccess = DB::table('students')
                        ->where('id', '=', $user->sid)
                        ->update(['parent_tel' => null]);

        if ($result && $ifSuccess) {
            Session::flash('success', 'Tanggungan Berjaya Dipadam');

            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Tanggungan Tidak Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        }
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
            // user role parent
            /* $organizations = Organization::whereHas('user', function ($query) use ($userId) {
                    $query->where('user_id', $userId)->Where(function ($query) {
                        $query->where('organization_user.role_id', '=', 2)
                        ->Orwhere('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5)
                        ->Orwhere('organization_user.role_id', '=', 6);
                    });
                })->get(); */

            $organs = DB::table('organizations as o')
                ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
                ->select('o.*')
                ->where('ou.user_id', $userId)
                ->whereIn('ou.role_id', [4, 5, 6, 12, 20, 21])
                ->get();

            

            $organizations = [];

            foreach ($organs as $organ)
            {
                array_push($organizations, $organ);
                $organ_children = DB::table('organizations')->where('parent_org', $organ->id)->get();

                if ($organ != null)
                {
                    foreach ($organ_children as $organ_child)
                    {
                        array_push($organizations, $organ_child);
                    }
                }
            }
            //dd($organizations);
            $organizations = array_unique($organizations, SORT_REGULAR);
            return $organizations;
        }
    }

    public function getParentByTel($telno)
    {
        $parent =  DB::table('users')
        ->leftJoin('organization_user', 'users.id', '=', 'organization_user.user_id')
        ->where('organization_user.role_id', '=', '6')
        ->where('users.telno', '=' ,$telno)
        ->get();
        // dd($telno);

        return  DB::table('users')
                ->leftJoin('organization_user', 'users.id', '=', 'organization_user.user_id')
                ->where('organization_user.role_id', '=', '6')
                ->where('users.telno', '=' ,$telno)
                ->get();
    }

    public function getParentDatatable(Request $request)
    {
        // dd($request->icno);

        if (request()->ajax()) {

            $userId = Auth::id();
            // $data = Parents::where('icno', $request->icno)->first();
            /* $data = DB::table('users')
                ->where('icno', $request->icno)
                ->get(); */

            $data = $this->getParentByTel($request->telno);

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

    public function parentImport(Request $request)
    {
        // dd($request->organ);
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (! in_array($etx, $formats)) {

            return redirect('/parent')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        Excel::import(new ParentsImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/parent')->with('success', 'Parents have been added successfully');
    }

    public function indexParentFeesHistory(){
        $organization = $this->getOrganizationByUserId();

        return view('parent.fee.history', compact('organization'));
    }
}