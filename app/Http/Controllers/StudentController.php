<?php

namespace App\Http\Controllers;

use App\Exports\StudentExport;
use App\Imports\StudentImport;
use App\Models\ClassModel;
use App\Models\Organization;
use App\Models\Student;
use App\Models\Parents;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Arabic;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Models\OrganizationRole;
use App\User;

class StudentController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();

        if(Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir'))
        {
            $listclass = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select('classes.id as id', 'classes.nama', 'classes.levelid')
                ->where([
                    ['class_organization.organization_id', $organization[0]->id],
                    ['classes.status', 1]
                ])
                ->orderBy('classes.nama')
                ->get();
        }
        else
        {
            $listclass = DB::table('class_organization')
                ->leftJoin('classes', 'class_organization.class_id', '=', 'classes.id')
                ->leftJoin('organization_user', 'class_organization.organ_user_id', 'organization_user.id')
                ->select('classes.id as id', 'classes.nama', 'classes.levelid')
                ->where([
                    ['class_organization.organization_id', $organization[0]->id],
                    ['classes.status', 1],
                    ['organization_user.user_id', $userId]
                ])
                ->orderBy('classes.nama')
                ->get();
        }

        return view("student.index", compact('listclass', 'organization'));
    }

    public function studentexport(Request $request)
    {
        $this->validate($request, [
            'organ'          =>  'required',
            'kelas'          =>  'required',
        ]);

        // dd($request->kelas, $request->organ);
        return Excel::download(new StudentExport($request->organ, $request->kelas), 'student.xlsx');
    }

    public function studentimport(Request $request)
    {
        $this->validate($request, [
            'classImport'          =>  'required',
        ]);

        // dd($request->classImport);

        $classID = $request->get('classImport');

        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);
        $public_path = $_SERVER['DOCUMENT_ROOT'];
        
        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (! in_array($etx, $formats)) {

            return redirect('/student')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        Excel::import(new StudentImport($classID), $public_path . '/uploads/excel/' . $namaFile);
        return redirect('/student')->with('success', 'New student has been added successfully');
    }


    public function create()
    {
        //
        $userid     = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        // dd($userid);

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $school->schoolid]
            ])
            ->orderBy('classes.nama')
            ->get();

        $organization = $this->getOrganizationByUserId();


        return view('student.add', compact('listclass', 'organization'));
    }

    public function store(Request $request)
    {
        $classid = $request->get('classes');

        $co = DB::table('class_organization')
            ->select('id', 'organization_id as oid')
            ->where('class_id', $classid)
            ->first();

        $this->validate($request, [
            'name'              =>  'required',
            'icno'              =>  'required|unique:students,icno',
            'classes'           =>  'required',
            'parent_name'       =>  'required',
            'parent_email'      =>  'required',
            'parent_icno'       =>  'required',
            'parent_phone'      =>  'required',
        ]);

        $ifExits = DB::table('users as u')
                    ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
                    ->where('u.email', '=', $request->get('parent_email'))
                    ->where('u.icno', '=', $request->get('parent_icno'))
                    ->where('u.telno', '=', $request->get('parent_phone'))
                    ->whereIn('ou.role_id', [5, 6])
                    ->get();
        
        // dd($request->get('parent_email'), $request->get('parent_icno'), $request->get('parent_phone'),  $ifExits);
        
        if(count($ifExits) == 0) { // if not teacher
            $this->validate($request, [
                'parent_icno'      =>  'required|unique:users,icno',
                'parent_email'     =>  'required|email|unique:users,email',
            ]);

            $newparent = new Parents([
                'name'           =>  strtoupper($request->get('parent_name')),
                'icno'           =>  $request->get('parent_icno'),
                'email'          =>  $request->get('parent_email'),
                'password'       =>  Hash::make('abc123'),
                'telno'          =>  $request->get('parent_phone'),
                'remember_token' =>  Str::random(40),
            ]);
            $newparent->save();
        }
        else { // add parent role
            $newparent = DB::table('users')
                        ->where('email', '=', "{$request->get('parent_email')}")
                        ->first();
        }

        DB::table('organization_user')->insert([
            'organization_id'   => $co->oid,
            'user_id'           => $newparent->id,
            'role_id'           => 6,
            'start_date'        => now(),
            'status'            => 1,
        ]);


        $ou = DB::table('organization_user')
                ->where('user_id', $newparent->id)
                ->where('organization_id', $co->oid)
                ->where('role_id', 6)
                ->first();

        $user = User::find($newparent->id);

        // role parent
        $rolename = OrganizationRole::find(6);
        $user->assignRole($rolename->nama);
        
        $student = new Student([
            'nama'          =>  $request->get('name'),
            'icno'          =>  $request->get('icno'),
            'gender'        =>  $request->get('gender'),
            'email'         =>  $request->get('email'),
        ]);

        $student->save();

        DB::table('class_student')->insert([
            'organclass_id'   => $co->id,
            'student_id'      => $student->id,
            'start_date'      => now(),
            'status'          => 1,
        ]);
        DB::table('organization_user_student')->insert([
            'organization_user_id'  => $ou->id,
            'student_id'            => $student->id
        ]);

        /* 
            - this has to change after all the features have done.
            - delete parent tel column in table `students`
        */
        DB::table('students')
            ->where('id', $student->id)
            ->update(['parent_tel' => $newparent->telno]);

        // check fee for new in student

        return redirect('/student')->with('success', 'New student has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('class_organization.organization_id', 'students.id as id', 'students.nama as studentname', 'students.icno', 'students.gender', 'classes.id as classid', 'classes.nama as classname', 'class_student.status', 'students.email')
            ->where([
                ['students.id', $id],
            ])
            ->orderBy('classes.nama')
            ->first();

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $student->organization_id]
            ])
            ->orderBy('classes.nama')
            ->get();

        $organization = $this->getOrganizationByUserId();
        return view('student.update', compact('student', 'organization', 'listclass'));
    }

    public function update(Request $request, $id)
    {
        //
        $classid = $request->get('classes');

        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required',
            'classes'       =>  'required',
        ]);

        $getOrganizationClass = DB::table('class_organization')
            ->where('class_id', $classid)
            ->first();

        // dd($getOrganizationClass);
        $student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.nama as classname', 'class_student.status')
            ->where([
                ['students.id', $id],
            ])
            ->update(
                [
                    'students.nama' => $request->get('name'),
                    'students.icno' => $request->get('icno'),
                    'students.gender' => $request->get('gender'),
                    'students.email' => $request->get('email'),
                    'class_student.organclass_id'    => $getOrganizationClass->id,
                ]
            );

        return redirect('/student')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        //
        $result = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.nama as classname', 'class_student.status')
            ->where([
                ['students.id', $id],
            ])
            ->update(
                [
                    'class_student.status' => 0,
                ]
            );


        if ($result) {
            Session::flash('success', 'Murid Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Murid Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function getStudentDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            // $oid = $request->oid;
            $classid = $request->classid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($classid != '' && !is_null($hasOrganizaton)) {
                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.nama as classname', 'class_student.status')
                    ->where([
                        ['classes.id', $classid],
                        ['class_student.status', 1],
                    ])
                    ->orderBy('students.nama');

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
                    $btn = $btn . '<a href="' . route('student.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                });

                $table->rawColumns(['status', 'action']);
                return $table->make(true);
            }

            // dd($data->oid);
        }
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {
            return Organization::all();
        } else {
            // user role guru
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();
        }
    }

    public function fetchClass(Request $request)
    {
        $userId = Auth::id();
        $oid = $request->get('oid');

        if(Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir'))
        {
            $list = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select('classes.id as cid', 'classes.nama as cname')
                ->where([
                    ['class_organization.organization_id', $oid],
                    ['classes.status', 1]
                ])
                ->orderBy('classes.nama')
                ->get();
        }
        else
        {
            $list = DB::table('class_organization')
                ->leftJoin('classes', 'class_organization.class_id', '=', 'classes.id')
                ->leftJoin('organization_user', 'class_organization.organ_user_id', 'organization_user.id')
                ->select('classes.id as cid', 'classes.nama as cname')
                ->where([
                    ['class_organization.organization_id', $oid],
                    ['classes.status', 1],
                    ['organization_user.user_id', $userId]
                ])
                ->orderBy('classes.nama')
                ->get();
        }
        return response()->json(['success' => $list]);
    }

    public function getStudentDatatableFees(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            // $oid = $request->oid;
            $classid = $request->classid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($classid != '' && !is_null($hasOrganizaton)) {
                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('students.*', 'class_student.fees_status')
                    ->where([
                        ['classes.id', $classid],
                        ['class_student.status', 1],
                    ])
                    ->orderBy('students.nama');

                $table = Datatables::of($data);

                $table->addColumn('gender', function ($row) {
                    if ($row->gender == 'L') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . 'Lelaki</div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . 'Perempuan</div>';

                        return $btn;
                    }
                });

                $table->addColumn('status', function ($row) {
                    if ($row->fees_status == 'Completed') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success">Selesai</span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Belum Selesai </span></div>';

                        return $btn;
                    }
                });


                $table->rawColumns(['gender', 'status']);
                return $table->make(true);
            }

            // dd($data->oid);
        }
    }

    public function generatePDFByClass(Request $request)
    {
        $class_id = $request->class_id;
        $class = ClassModel::where('id', $class_id)->first();

        $get_organization = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.*', 'classes.nama as classname')
            ->where([
                ['classes.id', $class_id],
            ])
            ->first();

        $data = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.*', 'class_student.fees_status')
            ->where([
                ['classes.id', $class_id],
                ['class_student.status', 1],
            ])
            ->orderBy('students.nama')
            ->get();

        $pdf = PDF::loadView('fee.report-search.template-pdf', compact('data', 'get_organization'));

        return $pdf->download($class->nama . '.pdf');
    }
}
