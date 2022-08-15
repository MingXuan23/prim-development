<?php

namespace App\Http\Controllers;

use App\Exports\OutingExport;
use App\Exports\DormExport;
use App\Imports\DormImport;
use App\Imports\ResidentImport;
use Illuminate\Http\Request;
use App\Models\Dorm;
use App\Models\Outing;
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

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class DormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    //
    //
    //index functions
    public function index()
    {
        //
        $organization = $this->getOrganizationByUserId();

        return view('dorm.outing.index', compact('organization'));
    }

    public function indexOuting()
    {
        // 
        $organization = $this->getOrganizationByUserId();

        return view('dorm.outing.index', compact('organization'));
    }

    public function indexResident()
    {
        // 
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();
        // dd($organization[0]->id);
        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Warden')) {
            $dorm = DB::table('dorms')
                ->join('class_student', 'class_student.dorm_id', '=', 'dorms.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->select()
                ->where([
                    ['class_organization.organization_id', $organization[0]->id],
                    ['class_student.status', 1],
                ])
                ->orderBy('dorms.name')
                ->get();
        }

        return view("dorm.resident.index", compact('dorm', 'organization'));
    }

    public function indexDorm()
    {
        // 
        $organization = $this->getOrganizationByUserId();

        return view('dorm.management.index', compact('organization'));
    }

    //
    //
    //import and export functions
    public function outingexport()
    {
        return Excel::download(new OutingExport, 'outing.xlsx');
    }

    public function dormexport(Request $request)
    {
        return Excel::download(new DormExport($request->organ), 'dorm.xlsx');
    }

    public function dormimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {

            return redirect('/dorm/dorm/indexDorm')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        Excel::import(new DormImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/dorm/dorm/indexDorm')->with('success', 'Dorms have been added successfully');
    }

    //not yet modify
    public function residentimport(Request $request, $id)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {

            return redirect('/dorm/dorm/indexDorm')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        //please check need what id to pass into ResidentImport
        Excel::import(new ResidentImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/dorm/dorm/indexDorm')->with('success', 'Residents have been added successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    //
    //
    //create or add files
    public function create()
    {
        //

    }

    public function createOuting()
    {
        //
        $organization = $this->getOrganizationByUserId();
        return view('dorm.outing.add', compact('organization'));
    }

    public function createResident()
    {
        //need list of organization and dorm
        //need logged in user id
        $userid     = Auth::id();
        $organization = $this->getOrganizationByUserId();

        $dormlist =  $this->getDormByOrganizationId();
        return view('dorm.resident.add', compact('dormlist', 'organization'));
    }

    public function createDorm()
    {
        //
        $organization = $this->getOrganizationByUserId();
        return view('dorm.management.add', compact('organization'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //
    //
    // store functions
    public function store(Request $request)
    {
        // 

    }

    public function storeOuting(Request $request)
    {
        // 
        $this->validate($request, [
            'start_date'        =>  'required',
            'end_date'          =>  'required',
            'organization'      =>  'required',
        ]);

        DB::table('outings')->insert([
            'start_date_time' => $request->get('start_date'),
            'end_date_time'   => $request->get('end_date'),
            'organization_id' => $request->get('organization'),
        ]);

        return redirect('/dorm/dorm/indexOuting')->with('success', 'New outing date and time has been added successfully');
    }

    public function storeResident(Request $request)
    {
        // 
        $this->validate($request, [
            'name'              =>  'required',
            'organization'      =>  'required',
            'parent_phone'      =>  'required',
            'dorm'              =>  'required'
        ]);

        $organizationid = $request->get('organization');
        $stdname = $request->get('name');
        $parentphone = $request->get('parent_phone');
        
        $dormid = $request->get('dorm');
        $newdormid = (int)$dormid;

        // find student id
        $student = DB::table('students')
            ->join('organization_user_student as ous', 'ous.student_id', '=', 'students.id')
            ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
            ->where('students.nama', $stdname)
            ->where('students.parent_tel', $parentphone)
            ->select('students.id', 'ou.organization_id as oid', 'ou.user_id as uid', 'ous.organization_user_id as ouid')
            ->get();

        // dd($student[0]->oid);
        // get organization_class id
        $orgclassid = DB::table('class_organization as co')
            ->where('co.organization_id', $student[0]->oid)
            ->select('co.id')
            ->get();


        DB::table('class_student')
            ->where('student_id', $student[0]->id)
            ->where('organclass_id', $orgclassid[0]->id)
            ->update(['dorm_id' => $newdormid]);

        return redirect('/dorm/dorm/indexResident')->with('success', 'New student has been added successfully');
    }

    //haven't modify yet
    public function storeDorm(Request $request)
    {
        // 
        $this->validate($request, [
            'name'        =>  'required',
            'capacity'    =>  'required',
            'organization'      =>  'required',
            //'name', 'accommodate_no', 'student_inside_no'
        ]);
        //echo ({{ $request->get('organization') }});

        DB::table('dorms')->insert([
            'name' => $request->get('name'),
            'accommodate_no'   => $request->get('capacity'),
            'organization_id' => $request->get('organization'),
            'student_inside_no' => 0
        ]);

        return redirect('/dorm/dorm/indexDorm')->with('success', 'New dorm has been added successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    //
    //
    // edit and update functions
    public function edit($id)
    {
        //

    }

    public function editOuting($id)
    {
        //  

        $outing = DB::table('outings')
            ->where('outings.id', $id)
            ->select('outings.id', 'outings.start_date_time', 'outings.end_date_time', 'outings.organization_id')
            ->first();

        $organization = $this->getOrganizationByUserId();

        return view('dorm.outing.update', compact('outing', 'organization', 'id'));
    }

    public function editDorm($id)
    {
        //  
        $dorm = DB::table('dorms')
            ->where('dorms.id', $id)
            ->select('dorms.id', 'dorms.name', 'dorms.accommodate_no', 'dorms.student_inside_no')
            //'name', 'accommodate_no', 'student_inside_no'
            ->first();

        $organization = $this->getOrganizationByUserId();

        return view('dorm.management.update', compact('dorm', 'organization', 'id'));
    }

    public function update(Request $request, $id)
    {
        //

    }

    public function updateOuting(Request $request, $id)
    {
        //
        // dd($id);
        $this->validate($request, [
            'start_date'        =>  'required',
            'end_date'          =>  'required',
            'organization'      =>  'required',
        ]);

        DB::table('outings')
            ->where('id', $id)
            ->update(
                [
                    'start_date_time' => $request->get('start_date'),
                    'end_date_time'   => $request->get('end_date')
                ]
            );

        // DB::table('class_organization')->where('class_id', $id)
        //     ->update([
        //         'organization_id' => $request->get('organization'),
        //         'organ_user_id'    =>  $request->get('classTeacher')
        //     ]);

        return redirect('/dorm/dorm/indexOuting')->with('success', 'The data has been updated!');
    }

    public function updateDorm(Request $request, $id)
    {
        //
        // dd($id);
        $this->validate($request, [
            'name'        =>  'required',
            'accommodate_no'    =>  'required',
            'organization'      =>  'required',
            //'name', 'accommodate_no', 'student_inside_no'

        ]);

        DB::table('dorms')
            ->where('id', $id)
            ->update(
                [
                    'name' => $request->get('name'),
                    'accommodate_no'   => $request->get('capacity'),
                    'student_inside_no'   => $request->get('studentno'),
                ]
            );

        // DB::table('class_organization')->where('class_id', $id)
        //     ->update([
        //         'organization_id' => $request->get('organization'),
        //         'organ_user_id'    =>  $request->get('classTeacher')
        //     ]);

        return redirect('/dorm/dorm/indexDorm')->with('success', 'The data has been updated!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //
    //
    // destroy functions
    public function destroy($id)
    {
        //
        $organization = $this->getOrganizationByUserId();
        return view('dorm.outing.add', compact('organization'));
    }

    public function destroyOuting($id)
    {
        //
        $result = DB::table('outings')->where('outings.id', $id);

        if ($result->delete()) {
            Session::flash('success', 'Outing Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Outing Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function destroyDorm($id)
    {
        //
        $result = DB::table('dorms')->where('dorms.id', $id);

        if ($result->delete()) {
            Session::flash('success', 'Dorm Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Dorm Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role pentadbir 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5)
                        ->Orwhere('organization_user.role_id', '=', 8);
                });
            })->get();
        }
    }

    public function getDormByOrganizationId()
    {
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();

        if (Auth::user()->hasRole('Superadmin')) {

            return Dorm::all();
        } else {
            // user role pentadbir 
        
            return DB::table('dorms')
            ->where('dorms.organization_id' , $organization[0]->id)
            ->select()
            ->get();
        }
    }

    //
    //
    //application functions
    public function updateOutTime($id)
    {
        // $asrama = Asrama::findOrFail($id);
        // $asrama->update(array('outing_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    public function updateInTime($id)
    {
        // $asrama = Asrama::findOrFail($id);
        // $asrama->update(array('in_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    public function updateOutArriveTime($id)
    {
        // $asrama = Asrama::findOrFail($id);
        // $asrama->update(array('out_arrive_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    public function updateInArriveTime($id)
    {
        // $asrama = Asrama::findOrFail($id);
        // $asrama->update(array('in_arrive_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    public function updateOutingTime($id)
    {
        // $outing = Outing::findOrFail($id);
        // $name = $request->input('stud_name');
        // DB::update('update student set name = ? where id = ?',[$name,$id]);
        // echo "Record updated successfully.<br/>";
        // echo '<a href = "/edit-records">Click Here</a> to go back.';

        // $outing->update(array('start_date_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    //
    //
    //get datatable functions
    public function getOutingsDatatable(Request $request)
    {
        // dd($request->oid);
        if (request()->ajax()) {
            $oid = $request->oid;
            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('outings')
                    ->select('outings.id', 'outings.start_date_time', 'outings.end_date_time')
                    ->where('outings.organization_id', $oid)
                    ->orderBy('outings.start_date_time');
            }

            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('dorm.editOuting', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function getDormDatatable(Request $request)
    {
        // dd($request->oid);
        if (request()->ajax()) {
            $oid = $request->oid;
            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('dorms')
                    ->select('dorms.id', 'dorms.name', 'dorms.accommodate_no', 'dorms.student_inside_no', 'dorms.organization_id')
                    ->where('dorms.organization_id', $oid)
                    ->orderBy('dorms.name');
                //'name', 'accommodate_no', 'student_inside_no'
            }

            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('importresident', $row->id) . '" class="btn btn-primary m-1">Import</a>';
                $btn = $btn . '<a href="' . route('dorm.editDorm', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function getResidentsDatatable(Request $request)
    {
        // dd($request->hasOrganization);
        if (request()->ajax()) {
            $oid = $request->oid;

            // $dormid = $request->dormid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('students.id as id', 'students.nama as studentname', 'classes.nama as classname', 'class_student.start_date_time', 'class_student.end_date_time', 'class_student.outing_status', 'class_student.blacklist')
                    ->where([
                        ['class_student.dorm_id', $oid],
                        ['class_student.status', 1],
                    ])
                    ->orderBy('students.nama')
                    ->get();

                $table = Datatables::of($data);

                $table->addColumn('outing_status', function ($row) {
                    if ($row->outing_status == '1') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Aktif </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Keluar </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('blacklist', function ($row) {
                    if ($row->blacklist == '1') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Ditahan </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Free </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('dorm.editOuting', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                });

                $table->rawColumns(['outing_status', 'blacklist', 'action']);
                return $table->make(true);
            }
        }
    }

    public function fetchDorm(Request $request)
    {
        $oid = $request->get('oid');

        $list = DB::table('dorms')
            ->where('dorms.organization_id', $oid)
            ->select()
            ->orderBy('dorms.name')
            ->get();

        return response()->json(['success' => $list]);
    }
}
