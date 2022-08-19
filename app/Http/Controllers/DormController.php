<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Exports\OutingExport;
use App\Exports\ResidentExport;
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

    public function indexResident($id)
    {
        // 
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();
        // dd($organization[0]->id);
        if (
            Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') ||
            Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Warden')
        ) {
            $dorm = DB::table('dorms')
                ->select('dorms.organization_id', 'dorms.id as id', 'dorms.name')
                ->where([
                    // ['organizations.id', $organization[0]->id],
                    ['dorms.id', $id],
                ])
                ->orderBy('dorms.name')
                ->get();
        }

        // dd($dorm);
        return view("dorm.resident.index", compact('dorm', 'organization'));
    }

    public function indexDorm()
    {
        // 
        $organization = $this->getOrganizationByUserId();

        $dormlist = DB::table('dorms')
            ->select('id', 'name')
            ->get();


        return view('dorm.management.index', compact('organization', 'dormlist'));
    }

    //
    //
    //import and export functions
    public function outingexport(Request $request)
    {
        $this->validate($request, [
            'organ'      =>  'required',
        ]);

        $filename = DB::table('organizations')
            ->where('organizations.id', $request->organ)
            ->value('organizations.nama');

        return Excel::download(new OutingExport($request->organ), $filename.' masa outing.xlsx');
    }

    public function dormexport(Request $request)
    {
        return Excel::download(new DormExport($request->organ), 'dorm.xlsx');
    }

    public function residentexport(Request $request)
    {
        $this->validate($request, [
            'organExport'      =>  'required',
            'dormExport'      =>  'required',
        ]);

        $filename = DB::table('dorms')
            ->where('dorms.id', $request->dormExport)
            ->value('dorms.name');

            // dd($filename);

        return Excel::download(new ResidentExport($request->organExport, $request->dormExport), $filename .' pelajar.xlsx');
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
    public function residentimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {

            return redirect('/dorm/dorm/indexDorm')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        //dorm id need to pass into ResidentImport
        $import_file = new ResidentImport($request->dorm, 5);
        $another_file = Excel::toArray($import_file,  public_path('/uploads/excel/' . $namaFile));

        //get the accomodate number for the particular dorm
        $accomodate_number = DB::table('dorms')
            ->where('id', $request->dorm)
            ->value('accommodate_no');

        $student_inside = DB::table('dorms')
            ->where('id', $request->dorm)
            ->value('student_inside_no');

        $total_student_add = $student_inside + sizeof($another_file[0]);

        //only if the number of row count + student inside is less than accomodate number
        if ($total_student_add <= $accomodate_number) {
            Excel::import(new ResidentImport($request->dorm, $total_student_add), public_path('/uploads/excel/' . $namaFile));

            return redirect('/dorm/dorm/indexDorm')->with('success', 'Residents have been added successfully');
        } else
            return redirect('/dorm/dorm/indexDorm')->with('fail', 'Residents have not been added successfully because the student added is out of capacity limit');
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
        //get user id
        // $userid     = Auth::id();
        $organization = $this->getOrganizationByUserId();

        $category = DB::table('classifications')
                    ->get();

        if(Auth::user()->hasRole('Penjaga'))
        {
            return view('dorm.create', compact('organization', 'category'));
        }
    }

    public function createOuting()
    {
        //
        $organization = $this->getOrganizationByUserId();
        return view('dorm.outing.add', compact('organization'));
    }

    public function createResident()
    {
        // $userid     = Auth::id();
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
            'email'             =>  'required',
            'dorm'              =>  'required'
        ]);

        $organizationid = $request->get('organization');
        $neworganizationid = (int)$organizationid;

        $stdname = $request->get('name');
        $stdemail = $request->get('email');

        $dormid = $request->get('dorm');
        $newdormid = (int)$dormid;

        // find student id
        $student = DB::table('students')
            ->where('students.nama', $stdname)
            ->where('students.email', $stdemail)
            ->select('students.id')
            ->get();

        $dorm = DB::table('dorms')
            ->where('dorms.id', $newdormid)
            ->get();

        if (
            Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') ||
            Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Warden')
        ) {
            if (isset($student[0]->id) && ($dorm[0]->student_inside_no < $dorm[0]->accommodate_no)) {
                $updateDetails = [
                    'cs.dorm_id' => $newdormid,
                    'cs.start_date_time' => now()->toDateTimeString(),
                    'cs.end_date_time' => NULL,
                    'cs.outing_status' => 0,
                    'cs.blacklist' => 0,
                ];

                $result = DB::table('class_student as cs')
                    ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                    ->where([
                        ['cs.student_id', $student[0]->id],
                        ['cs.dorm_id', NULL],
                        ['co.organization_id', $neworganizationid],
                        ['cs.status', 1],
                    ])
                    ->update($updateDetails);
            } else {
                $result = 0;
            }

            if ($result > 0) {
                DB::table('dorms')
                    ->where('dorms.id', $newdormid)
                    ->update(['student_inside_no' => $dorm[0]->student_inside_no + 1]);

                return redirect()->to('/dorm/dorm/indexResident/' . $newdormid)->with('success', 'New student has been added successfully');
            }
        }

        return redirect()->to('/dorm/dorm/indexResident/' . $newdormid)->withErrors(['Failed to add student into dorm', 'Possible problem: Dorm is full  |  Student already has accommodation']);
    }


    public function storeDorm(Request $request)
    {
        // 
        $this->validate($request, [
            'name'        =>  'required|unique:dorms',
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

    public function editResident($id)
    {
        //  
        // dd($id); class_student.id
        $resident = DB::table('dorms')
            ->join('class_student', 'class_student.dorm_id', '=', 'dorms.id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->select('dorms.id as dorm_id', 'dorms.organization_id', 'dorms.name as dormname', 'class_student.student_id as id', 'students.nama as studentname', 'students.email', 'students.parent_tel')
            ->where([
                ['class_student.id', $id],
                ['class_student.status', 1],
            ])
            ->orderBy('dorms.name')
            ->get();

        $dormlist = DB::table('dorms')
            ->join('organizations', 'organizations.id', '=', 'dorms.organization_id')
            ->select('dorms.id as id', 'dorms.name')
            ->where([
                ['dorms.organization_id', $resident[0]->organization_id]

            ])
            ->orderBy('dorms.name')
            ->get();

        $organization = $this->getOrganizationByUserId();
        return view('dorm.resident.update', compact('resident', 'dormlist', 'organization'));
    }

    public function getID($id)
    {
        return response()->json(["string" => $id]);
    }
    public function editDorm($id)
    {
        //  
        $dorm = DB::table('dorms')
            ->where('dorms.id', $id)
            ->select('dorms.id', 'dorms.name', 'dorms.accommodate_no', 'dorms.student_inside_no', 'organization_id')
            ->first();

        //calculate sum of resident inside this dorm
        $dorm_student_inside = DB::table('class_student')
            ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
            ->where('class_student.dorm_id', $id)
            ->count();

        $organization = $this->getOrganizationByUserId();

        return view('dorm.management.update', compact('dorm', 'organization', 'dorm_student_inside', 'id'));
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

    public function updateResident(Request $request, $id)
    {
        // dd($id);
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'dorm' => 'required',
            'organization' => 'required'
        ]);

        $organizationid = $request->get('organization');
        $neworganizationid = (int)$organizationid;

        $stdname = $request->get('name');
        $stdemail = $request->get('email');

        $dormid = $request->get('dorm');
        $newdormid = (int)$dormid;

        $dorm = DB::table('dorms')
            ->where([
                ['dorms.id', $newdormid],
            ])
            ->select('dorms.id as dormid', 'dorms.accommodate_no', 'dorms.student_inside_no')
            ->get();


        $resident = DB::table('class_student')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->where([
                ['class_student.id', $id],
                ['students.nama', $stdname],
                ['students.email', $stdemail]
            ])
            ->select('class_student.id as id', 'class_student.student_id', 'class_student.dorm_id')
            ->get();


        $olddormid = DB::table('dorms')
            ->where([
                ['dorms.id', $resident[0]->dorm_id],
            ])
            ->select('dorms.id as dormid', 'dorms.accommodate_no', 'dorms.student_inside_no')
            ->get();

        if (
            Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') ||
            Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Warden')
        ) {
            if (isset($resident[0]->id) && ($dorm[0]->student_inside_no < $dorm[0]->accommodate_no) && ($olddormid[0]->dormid != $newdormid)) {
                $updateDetails = [
                    'cs.dorm_id' => $newdormid,
                    'cs.start_date_time' => now()->toDateTimeString(),
                    'cs.end_date_time' => NULL,
                ];

                $result = DB::table('class_student as cs')
                    ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                    ->where([
                        ['cs.id', $id],
                        ['co.organization_id', $neworganizationid],
                        ['cs.student_id', $resident[0]->student_id],
                        ['cs.status', 1],
                    ])
                    ->update($updateDetails);
            } else {
                $result = 0;
            }

            if ($result > 0) {
                DB::table('dorms')
                    ->where('dorms.id', $newdormid)
                    ->update(['student_inside_no' => $dorm[0]->student_inside_no + 1]);

                DB::table('dorms')
                    ->where('dorms.id', $olddormid[0]->dormid)
                    ->update(['student_inside_no' => $olddormid[0]->student_inside_no - 1]);

                return redirect()->to('/dorm/dorm/indexResident/' . $newdormid)->with('success', 'New student has been added successfully');
            }
        }
        return redirect()->to('/dorm/dorm/indexResident/'.$newdormid)->withErrors(['Failed to add student into dorm', 'Possible problem: Dorm is full  |  Student not found  |  Student is reside in the dorm']);   
    }

    public function updateDorm(Request $request, $id)
    {
        //
        // dd($id);
        $this->validate($request, [
            'name'        =>  'required|unique:dorms',
            'capacity'    =>  'required',
            'organization'      =>  'required',
            //'name', 'accommodate_no', 'student_inside_no'

        ]);

        DB::table('dorms')
            ->where('id', $id)
            ->update(
                [
                    'name' => $request->get('name'),
                    'accommodate_no'   => $request->get('capacity'),
                ]
            );

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
        $result = DB::table('dorms')->where('dorms.id', $id)->delete();
        //return response()->json($result);
        //$strirng = "asd";
        if ($result) {
            Session::flash('success', 'Dorm Berjaya Dipadam');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        } else {
            Session::flash('error', 'Dorm Gagal Dipadam');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        }
    }

    public function clearDorm($id)
    {
        //
        $result = DB::table('class_student')->where('dorm_id', $id)->update(['dorm_id' => null]);

        if ($result) {
            DB::table('dorms')->where('id', $id)->update(['student_inside_no' => 0]);

            Session::flash('success', 'Dorm Berjaya Dikosongkan');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        } else {
            Session::flash('error', 'Dorm Gagal Dikosongkan');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        }
    }

    public function destroyResident($id)
    {

        // 有没有解决方法呢 不需要duplicate query
        $dorm = DB::table('dorms')
            ->join('class_student', 'class_student.dorm_id', '=', 'dorms.id')
            ->where('class_student.id', $id)
            ->get();

        $updateDetails1 = [
            'class_student.end_date_time' => now()->toDateTimeString(),
            'class_student.dorm_id' => NULL,
            'dorms.student_inside_no' => $dorm[0]->student_inside_no - 1,
        ];

        $result = DB::table('dorms')
            ->join('class_student', 'class_student.dorm_id', '=', 'dorms.id')
            ->where('class_student.id', $id)
            ->update($updateDetails1);

        if ($result) {
            Session::flash('success', 'Pelajar Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pelajar Gagal Dipadam');
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
                    $query->where('organization_user.role_id', '=', 4);
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
                ->where('dorms.organization_id', $organization[0]->id)
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
                // $btn = $btn . '<a href="' . route('importresident', $row->id) . '" class="btn btn-primary m-1">Import</a>';

                //try
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" data-toggle="modal" data-target="#modelId3" class="btn btn-primary m-1">Import</button>';

                $btn = $btn . '<a href="' . route('dorm.editDorm', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1 destroyDorm">Buang</button></div>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1 clearDorm">Clear</button></div>';
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
            // $oid = $request->oid;

            $dormid = $request->dormid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($dormid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('students.id as id', 'students.nama as studentname', 'classes.nama as classname', 'class_student.start_date_time', 'class_student.end_date_time', 'class_student.outing_status', 'class_student.blacklist')
                    ->where([
                        ['class_student.dorm_id', $dormid],
                        ['class_student.status', 1],
                    ])
                    ->orderBy('students.nama')
                    ->get();

                $table = Datatables::of($data);

                $table->addColumn('outing_status', function ($row) {
                    if ($row->outing_status == '0') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Dalam </span></div>';

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
                        $btn = $btn . '<span class="badge badge-danger"> Ya </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Tidak </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('dorm.editResident', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
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
