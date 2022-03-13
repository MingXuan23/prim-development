<?php

namespace App\Http\Controllers;

use App\Exports\ClassExport;
use App\Imports\ClassImport;
use App\Models\ClassModel;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ClassController extends Controller
{

    public function index()
    {
        $organization = $this->getOrganizationByUserId();
        return view('class.index', compact('organization'));
    }

    public function classexport()
    {
        return Excel::download(new ClassExport, 'class.xlsx');
    }

    public function classimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (! in_array($etx, $formats)) {

            return redirect('/class')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        Excel::import(new ClassImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/class')->with('success', 'New class has been added successfully');
    }

    public function create()
    {
        //
        $organization = $this->getOrganizationByUserId();
        return view('class.add', compact('organization'));
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'          =>  'required',
            'level'         =>  'required',
            'organization'  =>  'required',
            'classTeacher'  =>  'required'
        ]);

        $class = new ClassModel([
            'nama'          =>  $request->get('name'),
            'levelid'       =>  $request->get('level'),
            'status'       =>  "1",
        ]);
        $class->save();

        DB::table('class_organization')->insert([
            'organization_id' => $request->get('organization'),
            'class_id'        => $class->id,
            'organ_user_id'  =>  $request->get('classTeacher'),
            'start_date'      => now(),
        ]);

        return redirect('/class')->with('success', 'New class has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $organization = $this->getOrganizationByUserId();
        $class       = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->where('classes.id', $id)
            ->first();

        return view('class.update', compact('class', 'organization'));
    }

    public function update(Request $request, $id)
    {
        //
        // dd($id);
        $this->validate($request, [
            'name'          =>  'required',
            'level'         =>  'required',
            'organization'  =>  'required',
            'classTeacher'  =>  'required'
        ]);

        DB::table('classes')
            ->where('id', $id)
            ->update(
                [
                    'nama'      => $request->get('name'),
                    'levelid'   => $request->get('level')
                ]
            );

        DB::table('class_organization')->where('class_id', $id)
            ->update([
                'organization_id' => $request->get('organization'),
                'organ_user_id'    =>  $request->get('classTeacher')
            ]);

        return redirect('/class')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        $result = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->where('classes.id', $id)
            ->update([
                'classes.status' => "0",
            ]);

        if ($result) {
            Session::flash('success', 'Kelas Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Kelas Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function getClassesDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $oid = $request->oid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('classes')
                    ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                    ->leftJoin('organization_user', 'class_organization.organ_user_id', 'organization_user.id')
                    ->leftJoin('users', 'organization_user.user_id', 'users.id')
                    ->select('classes.id as cid', 'classes.nama as cnama', 'classes.levelid', 'users.name as guru')
                    ->where([
                        ['class_organization.organization_id', $oid],
                        ['classes.status', "1"]
                    ])
                    ->orderBy('classes.nama')
                    ->orderBy('classes.levelid');
            }
            // dd($data->oid);
            $table = Datatables::of($data);

            $table->addColumn('gkelas', function ($row) {
                if ($row->guru === NULL) {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger">Tiada Guru Kelas</span></div>';

                    return $btn;
                } else {
                    return $btn = '<div class="d-flex justify-content-center">' . $row->guru . '</div>';
                }
            });

            $table->addColumn('totalstudent', function ($row) {

                $list_student = DB::table('class_organization')
                    ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->join('students', 'students.id', '=', 'class_student.student_id')
                    ->select('classes.nama', DB::raw('COUNT(students.id) as totalstudent'))
                    ->where('classes.id', $row->cid)
                    ->where('class_student.status', 1)
                    ->groupBy('classes.nama')
                    ->first();

                if ($list_student) {
                    $btn = '<div class="d-flex justify-content-center">' . $list_student->totalstudent . '</div>';
                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center"> 0 </div>';
                    return $btn;
                }
            });

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('class.edit', $row->cid) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->cid . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['totalstudent', 'action', 'gkelas']);
            return $table->make(true);
        }
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role pentadbir n guru 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5);
                });
            })->get();
        }
    }

    public function fetchTeacher(Request $request)
    {
        $listTeacher = DB::table('users as u')
        ->leftJoin('organization_user as ou', 'ou.user_id', 'u.id')
        ->select('ou.id as id', 'u.name')
        ->where('ou.organization_id', $request->oid)
        ->where('ou.role_id', 5)
        ->orderBy('u.name')
        ->get();
        
        return response()->json(['success' => $listTeacher]);
    }
}
