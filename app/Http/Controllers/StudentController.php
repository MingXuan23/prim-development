<?php

namespace App\Http\Controllers;

use App\Exports\StudentExport;
use App\Imports\StudentImport;
use App\Models\ClassModel;
use App\Models\Organization;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class StudentController extends Controller
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

        // dd($userid);

        $student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.nama as classname', 'class_student.status')
            ->where([
                ['class_organization.organization_id', $school->schoolid],
            ])
            ->orderBy('classes.nama')
            ->orderBy('students.nama')
            ->get();


        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $school->schoolid]
            ])
            ->orderBy('classes.nama')
            ->get();


        // dd($listclass);

        $organization = $this->getOrganizationByUserId();

        return view("student.index", compact('student', 'listclass', 'organization'));
    }

    public function studentexport()
    {
        return Excel::download(new StudentExport, 'student.xlsx');
    }

    public function studentimport(Request $request)
    {
        $this->validate($request, [
            'kelas'          =>  'required',
        ]);

        $classID = $request->get('kelas');

        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        Excel::import(new StudentImport($classID), public_path('/uploads/excel/' . $namaFile));
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
        //
        $classid = $request->get('classes');

        $co = DB::table('class_organization')
            ->select('id')
            ->where('class_id', $classid)
            ->first();

        // dd($co->id);

        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|numeric',
            'classes'       =>  'required',
        ]);

        $student = new Student([
            'nama'          =>  $request->get('name'),
            'icno'          =>  $request->get('icno'),
        ]);

        $student->save();

        DB::table('class_student')->insert([
            'organclass_id'   => $co->id,
            'student_id'      => $student->id,
            'start_date'      => now(),
            'status'          => 1,
        ]);

        return redirect('/student')->with('success', 'New student has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $listclass = ClassModel::all();
        $student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('class_organization.organization_id', 'students.id as id', 'students.nama as studentname', 'students.icno', 'classes.id as classid', 'classes.nama as classname', 'class_student.status')
            ->where([
                ['students.id', $id],
            ])
            ->orderBy('classes.nama')
            ->first();

        $organization = $this->getOrganizationByUserId();

        return view('student.update', compact('student', 'organization', 'listclass'));
    }

    public function update(Request $request, $id)
    {
        //
        $classid = $request->get('classes');

        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|numeric',
            'classes'       =>  'required',
        ]);

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
                    'classes.id'    => $classid,
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
            Session::flash('success', 'Guru Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Guru Gagal Dipadam');
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
}
