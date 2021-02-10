<?php

namespace App\Http\Controllers;

use App\Exports\StudentExport;
use App\Imports\StudentImport;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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

        return view("pentadbir.student.index", compact('student', 'listclass'));
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

        return view('pentadbir.student.add', compact('listclass'));
    }

    public function store(Request $request)
    {
        //
        $classid = $request->get('kelas');

        $co = DB::table('class_organization')
            ->select('id')
            ->where('class_id', $classid)
            ->first();

        // dd($co->id);

        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|numeric',
            'kelas'         =>  'required',
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
        //
        $userid     = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $school->schoolid]
            ])
            ->orderBy('classes.nama')
            ->get();



        $student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.id as classid', 'classes.nama as classname', 'class_student.status')
            ->where([
                ['students.id', $id],
            ])
            ->orderBy('classes.nama')
            ->first();
        // dd($listclass);
        // $student = DB::table('students')->where('id', $id)->first();

        return view('pentadbir.student.update', compact('student', 'listclass'));
    }

    public function update(Request $request, $id)
    {
        //
        $classid = $request->get('kelas');

        // $co = DB::table('class_student')
        //     ->select('organclass_id')
        //     ->where('student_id', $id)
        //     ->first();

        $co = DB::table('class_organization')
            ->select('id')
            ->where('class_id', $classid)
            ->first();

        $this->validate($request, [
            'name'          =>  'required',
            'icno'          =>  'required|numeric',
            'kelas'         =>  'required',
        ]);

        $student = DB::table('students')
            ->where('id', $id)
            ->update(
                [
                    'nama' => $request->get('name'),
                    'icno' => $request->get('icno')
                ]
            );

        $class = DB::table('class_student')
            ->where('student_id', $id)
            ->update(
                [
                    'organclass_id' => $co->id,
                ]
            );

        return redirect('/student')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        //
    }
}
