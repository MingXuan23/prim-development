<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Fee;
use App\Models\Fee_New;
use App\Models\Category;
use App\Models\ClassModel;
use App\Models\Transaction;
use App\Models\Organization;
use Illuminate\Http\Request;
use Psy\Command\WhereamiCommand;
use Yajra\DataTables\DataTables;
use App\Exports\ExportYuranStatus;
use App\Exports\ExportYuranStatusSwasta;
use App\Exports\ExportJumlahBayaranIbuBapa;
use App\Exports\ExportJumlahBayaranIbuBapaSwasta;
use App\Exports\ExportYuranOverview;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AppBaseController;
use Symfony\Component\VarDumper\Cloner\Data;

class FeesController extends AppBaseController
{
    public function index()
    {
        //
        $fees = DB::table('fees')->orderBy('nama')->get();
        $organization = $this->getOrganizationByUserId();
        $listcategory = DB::table('categories')->get();
        return view('pentadbir.fee.index', compact('fees', 'listcategory', 'organization'));
    }



    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        return view('pentadbir.fee.add', compact('organization'));
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // get type org
        // get year from class name
        $fee = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('classes', 'class_organization.class_id', '=', 'classes.id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount', 'organizations.id as organization_id', 'organizations.type_org', 'classes.nama')
            ->where('fees.id', $id)
            ->first();

        $aa = $fee->nama;
        $getyear = substr($aa, 0, 1);

        $getallclass = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as classname')
            ->where('organizations.id', $fee->organization_id)
            ->where('classes.nama', 'LIKE', '%' . $getyear . '%')
            ->orderBy('classes.nama')
            ->get();

        $getclass = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('classes', 'class_organization.class_id', '=', 'classes.id')
            ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount', 'class_organization.organization_id', 'classes.id as cid', 'classes.nama as classname')
            ->where('fees.id', $id)
            ->orderBy('classes.nama')
            ->get();

        // $getclassid = $getclass->cid;

        // dd($getclass);
        $organization = $this->getOrganizationByUserId();
        return view('pentadbir.fee.update', compact('fee', 'organization', 'getyear', 'getclass', 'getallclass'));
    }

    public function update(Request $request, $id)
    {
        $class = $request->get('cb_class');

        $req = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname', 'class_organization.id as co_id')
            ->where('organizations.id', $request->get('organization'))
            ->whereIn('classes.id', $class)
            ->get()->toArray();

        //get all class that have been store with that fees
        $getclassfees = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('classes', 'class_organization.class_id', '=', 'classes.id')
            ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount', 'class_organization.organization_id', 'classes.id as cid', 'classes.nama as classname')
            ->where('fees.id', $id)
            ->get()->toArray();


        for ($i = 0; $i < count($req); $i++) {

            //check if that kelas (in request) have been store with that fees or not
            $query = DB::table('fees')
                ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
                ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
                ->join('classes', 'class_organization.class_id', '=', 'classes.id')
                ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount', 'class_organization.organization_id', 'classes.id as cid', 'classes.nama as classname')
                ->where('fees.id', $id)
                ->where('class_fees.class_organization_id', $req[$i]->co_id)
                ->first();

            for ($j = 0; $j < count($getclassfees); $j++) {
                if (is_null($query)) {
                    // dd('haha');

                    DB::table('class_fees')->insert([
                        'status'                =>  '1',
                        'class_organization_id' =>  $req[$i]->co_id,
                        'fees_id'               =>  $id
                    ]);
                } elseif ($req[$i]->co_id != $getclassfees[$j]) {
                    DB::table('class_fees')
                        ->where('fees_id', $id)
                        ->update([
                            'status'                =>  '0'
                        ]);
                } else {
                    DB::table('class_fees')
                        ->where('fees_id', $id)
                        ->update([
                            'status'                =>  '1',
                            'class_organization_id' =>  $req[$i]->co_id
                        ]);
                }
            }
        }
    }

    public function destroy($id)
    {
        $result = DB::table('fees_new')
            ->where('id', '=', $id)
            ->delete();
        
       /*  $result = DB::table('fees_new')
            ->where('id', '=', $id)
            ->update([
                'status'        =>  '0'
            ]); */

        if ($result) {
            Session::flash('success', 'Yuran Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Yuran Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {
            return Organization::all();

            //wan add pentadbir swasta
        } elseif (Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Koop Admin') || Auth::user()->hasRole('Pentadbir Swasta') || Auth::user()->hasRole('Guru Swasta')) {

            // user role pentadbir n guru
            /* $temp_organs= Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5)
                        ->Orwhere('organization_user.role_id', '=', 12);
                });
            })->get(); */

            $organs = DB::table('organizations as o')
                ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
                ->select('o.*')
                ->where('ou.user_id', $userId)
                ->whereNull('o.deleted_at')
                ->whereIn('ou.role_id', [4, 5 ,12, 20, 21])
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

            return $organizations;
        } else {
            // user role ibu bapa
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('role_id', '6')->OrWhere('role_id', '7')->OrWhere('role_id', '8');
            })->get();
        }
    }

    public function fetchYear(Request $request)
    {
        $oid = $request->get('oid');
        $category = Category::where('organization_id', $oid)->get();

        $list = DB::table('organizations')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
            ->where('organizations.id', $oid)
            ->first();

        return response()->json(['success' => $list, 'category' => $category]);
    }


    public function fetchClass(Request $request)
    {

        // dd($request->get('schid'));
        $oid    = $request->get('oid');
        $year   = $request->get('year');

        $organization = Organization::find($oid);
        
        if ($organization->parent_org != null)
        {
            $oid = $organization->parent_org;
        }

        // dd($year);

        $list = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname')
            ->where('organizations.id', $oid)
            ->where('classes.nama', 'LIKE',  $year . '%')
            ->where('classes.status', 1)
            ->orderBy('classes.nama')
            ->get();

        return response()->json(['success' => $list]);
    }

    public function feesReport()
    {
        $organization = $this->getOrganizationByUserId();

        $all_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->count();

        // dd($all_student);
        $student_complete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->where('class_student.fees_status', 'Completed')
            ->count();

        $student_notcomplete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->where('class_student.fees_status', 'Not Complete')
            ->count();

        $all_parent =  DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->count();

        $parent_complete =  DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Completed')
            ->count();

        $parent_notcomplete =  DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Not Complete')
            ->count();

        // dd($all_student);

        return view('fee.report', compact('organization', 'all_student', 'student_complete', 'student_notcomplete', 'all_parent', 'parent_complete', 'parent_notcomplete'));
    }

    public function feesReportByOrganizationId(Request $request)
    {
        set_time_limit(120);
        $organization = Organization::find($request->oid);
        $oid=$organization->parent_org != null ? $organization->parent_org: $organization->id;
        //makesure student from parent_org is fetched
        $all_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', $oid)
            ->select('class_student.id as csid');
        
        foreach($all_student->get() as $s){
            $check_debt = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->select('students.*')
            ->where('class_student.id',$s->csid)
            ->where('student_fees_new.status', 'Debt')
            ->count();

            if ($check_debt == 0) {
                DB::table('class_student')
                    ->where('id', $s->csid)
                    ->update(['fees_status' => 'Completed']);

            }
        }
        // dd($all_student);
        // $student_complete = DB::table('students')
        //     ->join('class_student', 'class_student.student_id', '=', 'students.id')
        //     ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
        //     ->where('class_organization.organization_id', $oid)
        //     ->where('class_student.fees_status', 'Completed')
        //     ->count();
        $student_complete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes','classes.id','=','class_organization.class_id')
            ->where('classes.levelid','>',0)
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.fees_status', 'Completed')
            ->count();
        $student_notcomplete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes','classes.id','=','class_organization.class_id')
            ->where('classes.levelid','>',0)
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.fees_status', 'Not Complete')
            ->count();
        $all_student= $student_complete +$student_notcomplete ;

        $oid=$request->oid;//change back the children org if necessary
        $all_parent =  DB::table('organization_user')
            ->where('organization_id', $oid)
            //->whereIn('organization_id',[160,159,154,153,152,151,150,149,148,147,146,145,144,143,142,141,137,127,107,106,93,88,80])
            ->where('role_id', 6)
            ->where('status', 1);
            
        
        foreach($all_parent->get() as $p){
            $check_debt = DB::table('organization_user')
            ->join('fees_new_organization_user', 'fees_new_organization_user.organization_user_id', '=', 'organization_user.id')
            ->where('organization_user.id', $p->id)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Debt')
            ->count();
    
            if ($check_debt == 0) {
                
                DB::table('organization_user')
                    ->where('id', $p->id)
                    ->where('role_id', 6)
                    ->where('status', 1)
                    ->update(['fees_status' => 'Completed']);
            }
        }
      
       

        // $parent_complete =  DB::table('organization_user')
        //     ->where('organization_id', $oid)
        //     ->where('role_id', 6)
        //     ->where('status', 1)
        //     ->where('fees_status', 'Completed')
        //     ->count();
        $parent_complete =  DB::table('organization_user')
            ->join('organization_user_student','organization_user.id','=','organization_user_student.organization_user_id')
            ->join('students','students.id','organization_user_student.student_id')
            ->join('class_student','class_student.student_id','students.id')
            ->join('class_organization','class_organization.id','class_student.organclass_id')
            ->join('classes','classes.id','class_organization.class_id')
            ->where('classes.levelid','>',0)
            ->where('organization_user.organization_id', $oid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('organization_user.fees_status', 'Completed')
            ->distinct('organization_user.user_id')
            ->count();

        $parent_notcomplete =  DB::table('organization_user')
            ->join('organization_user_student','organization_user.id','=','organization_user_student.organization_user_id')
            ->join('students','students.id','organization_user_student.student_id')
            ->join('class_student','class_student.student_id','students.id')
            ->join('class_organization','class_organization.id','class_student.organclass_id')
            ->join('classes','classes.id','class_organization.class_id')
            ->where('classes.levelid','>',0)
            ->where('organization_user.organization_id', $oid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('organization_user.fees_status', 'Not Complete')
            ->distinct('organization_user.user_id')
            ->count();
            $all_parent=$parent_complete + $parent_notcomplete;
        return response()->json(['all_student' => $all_student, 'student_complete' => $student_complete, 'student_notcomplete' => $student_notcomplete, 'all_parent' => $all_parent, 'parent_complete' => $parent_complete, 'parent_notcomplete' => $parent_notcomplete]);

    }

    public function feesReportByClassId(Request $request)
    {
        //$organId = $request->oid;
        $classId = $request->cid;
        $feeId  = $request->fid;

        $total_student = DB::table('class_student as cs')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('student_fees_new as sfn', 'sfn.class_student_id', '=', 'cs.id')
            ->where('cs.status', 1)
            //->where('co.organization_id', $organId)
            ->where('co.class_id', $classId)
            ->where('sfn.fees_id', $feeId)
            ->count();

        $total_student_paid = DB::table('class_student as cs')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('student_fees_new as sfn', 'sfn.class_student_id', '=', 'cs.id')
            ->where('cs.status', 1)
            //->where('co.organization_id', $organId)
            ->where('co.class_id', $classId)
            ->where('sfn.fees_id', $feeId)
            ->where('sfn.status', 'Paid')
            ->count();

        $total_student_debt = DB::table('class_student as cs')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('student_fees_new as sfn', 'sfn.class_student_id', '=', 'cs.id')
            ->where('cs.status', 1)
            //->where('co.organization_id', $organId)
            ->where('co.class_id', $classId)
            ->where('sfn.fees_id', $feeId)
            ->where('sfn.status', 'Debt')
            ->count();

        return response()->json(['total_student' => $total_student, 'total_student_paid' => $total_student_paid, 'total_student_debt' => $total_student_debt]);
    }

    public function reportByClass($type, $class_id)
    {
        $class = DB::table('classes')
            ->where('id', $class_id)->first();

        return view('fee.reportbyclass', compact('type', 'class'));
    }

    public function getTypeDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $type = $request->type;
            $oid = $request->oid;
            // dd($type);
            $userId = Auth::id();

            if ($type == 'Selesai') {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_organization.organization_id as oid', 'classes.id', 'classes.nama', DB::raw('COUNT(students.id) as totalstudent'), 'class_student.fees_status')
                    ->where('class_organization.organization_id', $oid)
                    ->where('class_student.fees_status', 'Completed')
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->get();
            } else {
                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_organization.organization_id as oid', 'classes.id', 'classes.nama', DB::raw('COUNT(students.id) as totalstudent'), 'class_student.fees_status')
                    ->where('class_organization.organization_id', $oid)
                    ->where('class_student.fees_status', 'Not Complete')
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->get();
            }

            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('total', function ($row) {

                $first = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('classes.nama', DB::raw('COUNT(students.id) as totalallstudent'))
                    ->where('class_organization.organization_id', $row->oid)
                    ->where('classes.id', $row->id)
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->first();

                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . $row->totalstudent . '/' . $first->totalallstudent . '</div>';
                return $btn;
            });

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('fees.reportByClass', ['type' => $row->fees_status, 'class_id' => $row->id]) . '"" class="btn btn-primary m-1">Butiran</a></div>';
                // $btn = $btn . '<a href="' . route('fees.edit', $row->feeid) . '" class="btn btn-primary m-1">Edit</a>';
                // $btn = $btn . '<button id="' . $row->feeid . '" data-token="' . $token . '" class="btn btn-danger m-1">Details</button></div>';
                return $btn;
            });

            $table->rawColumns(['total', 'action']);
            return $table->make(true);
        }
    }

    public function getParentDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $type = $request->type;
            $oid = $request->oid;
            // dd($type);
            $userId = Auth::id();

            if ($type == 'Selesai') {

                // $data = DB::table('users')
                //     ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                //     ->select('users.*', 'organization_user.organization_id')
                //     ->where('organization_user.organization_id', $oid)
                //     ->where('organization_user.role_id', 6)
                //     ->where('organization_user.status', 1)
                //     ->where('organization_user.fees_status', 'Completed')
                //     ->get();
                $data =  DB::table ('users')
                    ->join('organization_user','users.id', '=' ,'organization_user.user_id')
                    ->join('organization_user_student','organization_user.id','=','organization_user_student.organization_user_id')
                    ->join('students','students.id','organization_user_student.student_id')
                    ->join('class_student','class_student.student_id','students.id')
                    ->join('class_organization','class_organization.id','class_student.organclass_id')
                    ->join('classes','classes.id','class_organization.class_id')
                    ->where('classes.levelid','>',0)
                    ->where('organization_user.organization_id', $oid)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('organization_user.fees_status', 'Completed')
                    ->select('users.*', 'organization_user.organization_id')
                    ->distinct('users.id')
                    ->get();
     
            } else {
                // $data = DB::table('users')
                //     ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                //     ->select('users.*', 'organization_user.organization_id')
                //     ->where('organization_user.organization_id', $oid)
                //     ->where('organization_user.role_id', 6)
                //     ->where('organization_user.status', 1)
                //     ->where('organization_user.fees_status', 'Not Complete')
                //     ->get();

                $data =  DB::table ('users')
                    ->join('organization_user','users.id', '=' ,'organization_user.user_id')
                    ->join('organization_user_student','organization_user.id','=','organization_user_student.organization_user_id')
                    ->join('students','students.id','organization_user_student.student_id')
                    ->join('class_student','class_student.student_id','students.id')
                    ->join('class_organization','class_organization.id','class_student.organclass_id')
                    ->join('classes','classes.id','class_organization.class_id')
                    ->where('classes.levelid','>',0)
                    ->where('organization_user.organization_id', $oid)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('organization_user.fees_status', 'Not Complete')
                    ->select('users.*', 'organization_user.organization_id')
                    ->distinct('users.id')
                    ->get();
            }

            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a class="btn btn-primary m-1 user-id" id="' . $row->id . '-' . $row->organization_id . '">Butiran</a></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }


    public function getstudentDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $status = $request->status;
            $class_id = $request->class_id;
            // dd($type);
            $userId = Auth::id();

            $data = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('students.*')
                ->where('classes.id', $class_id)
                ->where('class_student.fees_status', $status)
                ->orderBy('students.nama')
                ->get();
            //$this->validateStatus($data);
            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a class="btn btn-primary m-1 student-id" id="' . $row->id . '">Butiran</a></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function CategoryA()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_A.index', compact('organization'));
    }

    public function createCategoryA()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_A.add', compact('organization'));
    }

    public function StoreCategoryA(Request $request)
    {
        $price          = $request->get('price');
        $quantity       = $request->get('quantity');
        $oid            = $request->get('organization');
        $date_started   = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end       = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');
        $total          = $price * $quantity;

        $target = ['data' => 'ALL'];

        // $target = json_encode($data);

        // dd($target);

        $fee = new Fee_New([
            'name'              =>  $request->get('name'),
            'desc'              =>  $request->get('description'),
            'category'          =>  "Kategori A",
            'quantity'          =>  $request->get('quantity'),
            'price'             =>  $request->get('price'),
            'totalAmount'       =>  $total,
            'start_date'        =>  $date_started,
            'end_date'          =>  $date_end,
            'status'            =>  "1",
            'target'            =>  $target,
            'organization_id'   =>  $oid,
        ]);

        // dd($fee);

        if ($fee->save()) {
            $parent_id = DB::table('organization_user as ou')
                ->where('organization_id', $oid)
                ->where('role_id', 6)
                ->where('status', 1)
                ->get();

            // to make sure one parent would recieve one only katagory fee if he or she hv more than children in school
            for ($i = 0; $i < count($parent_id); $i++) {
                $activeChildren= DB::table('organization_user_student as ous')
                        ->join('students as s' ,'s.id','ous.student_id')
                        ->join('class_student as cs','cs.student_id','s.id')
                        ->join('class_organization as co','co.id','cs.organclass_id')
                        ->join('classes as c','c.id','co.class_id')
                        ->where('c.levelid','>',0)
                        ->where('ous.organization_user_id',$parent_id[$i]->id)
                        ->select('s.id')
                        ->distinct()
                        ->get();
                if(count($activeChildren)>0){
                    $fees_parent = DB::table('organization_user')
                    ->where('id', )
                    ->update(['fees_status' => 'Not Complete']);

                DB::table('fees_new_organization_user')->insert([
                    'status' => 'Debt',
                    'fees_new_id' => $fee->id,
                    'organization_user_id' => $parent_id[$i]->id,
                ]);
                }
               
            }

            return redirect('/fees/A')->with('success', 'Yuran Kategori A telah berjaya dimasukkan');
        }
    }

    public function getCategoryDatatable(Request $request)
    {
        if (request()->ajax()) {
            $oid = $request->oid;
            $category = $request->category;
            $userId = Auth::id();

            if ($oid != '') {

                // $data = DB::table('fees')->orderBy('nama')->get();

                if ($category == "A") {
                    $data     = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategori A")
                        ->where('status', "1")
                        ->get();
                    
                    foreach($data as $d)
                    {
                        $d->target = "Setiap Keluarga";
                    }

                } elseif ($category == "B") {
                    $data     = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategori B")
                        ->where('status', "1")
                        ->get();
                    
                    foreach($data as $d)
                    {
                        $level = json_decode($d->target);
                        if($level->data == "All_Level")
                        {
                            $d->target = "Semua Tahap";
                        }
                        elseif($level->data  == 1)
                        {
                            $d->target = "Kelas : Tahap 1";
                        }
                        elseif($level->data  == 2)
                        {
                            $d->target = "Kelas : Tahap 2";
                        }
                        elseif(is_array($level->data))
                        {
                            $classes = DB::table('classes')
                                        ->whereIN('id', $level->data)
                                        ->get();
                            
                            $d->target = "Kelas : ";
                            foreach($classes as $i=>$class)
                            {
                                $d->target = $d->target .  $class->nama  . (sizeof($classes) - 1 == $i ? "" : ", ");
                            }
                        }
                    }

                } elseif($category == "C") {
                    $data     = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategori C")
                        ->where('status', "1")
                        ->get();
                    
                    foreach($data as $d)
                    {
                        $level = json_decode($d->target);
                        $d->target = "Jantina : " . ($level->gender == 'L' ? "Lelaki<br>" : "Perempuan<br>");
                        if($level->data == "All_Level")
                        {
                            $d->target = $d->target . "Kelas : Semua Tahap";
                        }
                        elseif($level->data  == 1)
                        {
                            $d->target = $d->target . "Kelas : Tahap 1";
                        }
                        elseif($level->data  == 2)
                        {
                            $d->target = $d->target . "Kelas : Tahap 2";
                        }
                        elseif(is_array($level->data))
                        {
                            $classes = DB::table('classes')
                                        ->whereIN('id', $level->data)
                                        ->get();
                            
                            $d->target = $d->target . $d->target = "Kelas : ";
                            foreach($classes as $i=>$class)
                            {
                                $d->target = $d->target .  $class->nama  . (sizeof($classes) - 1 == $i ? "" : ", ");
                            }
                        }
                    }
                } elseif($category == "Recurring") {
                    $data     = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategori Berulang")
                        ->where('status', "1")
                        ->get();
                    
                    foreach($data as $d)
                    {
                        $level = json_decode($d->target);
                        if($level->data == "All_Level")
                        {
                            $d->target = "Semua Tahap";
                        }
                        elseif($level->data  == 1)
                        {
                            $d->target = "Kelas : Tahap 1";
                        }
                        elseif($level->data  == 2)
                        {
                            $d->target = "Kelas : Tahap 2";
                        }
                        elseif(is_array($level->data))
                        {
                            $classes = DB::table('classes')
                                        ->whereIN('id', $level->data)
                                        ->get();
                            
                            $d->target = "Kelas : ";
                            foreach($classes as $i=>$class)
                            {
                                $d->target = $d->target .  $class->nama  . (sizeof($classes) - 1 == $i ? "" : ", ");
                            }
                        }
                    }
                }
            }

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

            /* $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                // $btn = $btn . '<a href="' . route('fees.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            }); */

            // $table->rawColumns(['status', 'action']);
            $table->rawColumns(['target', 'status']);
            return $table->make(true);
        }
    }

    public function CategoryB()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_B.index', compact('organization'));
    }

    public function createCategoryB()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_B.add', compact('organization'));
    }


    public function StoreCategoryB(Request $request)
    {
        $gender         = "";
        $class          = $request->get('cb_class');
        $level          = $request->get('level');
        $year           = $request->get('year');
        $name           = $request->get('name');
        $price          = $request->get('price');
        $quantity       = $request->get('quantity');
        $desc           = $request->get('description');
        $oid            = $request->get('organization');
        $date_started   = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end       = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');
        $total          = $price * $quantity;
        $category       = "Kategori B";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } elseif ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function CategoryC()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_C.index', compact('organization'));
    }

    public function createCategoryC()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_C.add', compact('organization'));
    }

    public function StoreCategoryC(Request $request)
    {
        // dd($request->toArray());
        $gender     = $request->get('gender');
        $class      = $request->get('cb_class');
        $level      = $request->get('level');
        $year       = $request->get('year');
        $name       = $request->get('name');
        $price          = $request->get('price');
        $quantity       = $request->get('quantity');
        $desc           = $request->get('description');
        $oid            = $request->get('organization');
        $date_started   = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end       = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');
        $total          = $price * $quantity;
        $category       = "Kategori C";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } elseif ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function CategoryRecurring()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.recurring.index', compact('organization'));
    }

    public function createCategoryRecurring()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.recurring.add', compact('organization'));
    }


    public function StoreCategoryRecurring(Request $request)
    {
        $gender         = "";
        $class          = $request->get('cb_class');
        $level          = $request->get('level');
        $year           = $request->get('year');
        $name           = $request->get('name');
        $price          = $request->get('price');
        $quantity       = $request->get('quantity');
        $desc           = $request->get('description');
        $oid            = $request->get('organization');
        $date_started   = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end       = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');
        $total          = $price * $quantity;
        $category       = "Kategori Berulang";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } elseif ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function fetchClassYear(Request $request)
    {

        // dd($request->get('level'));
        $level = $request->get('level');
        $oid = $request->get('oid');
        
        $organization = Organization::find($oid);
        
        if ($organization->parent_org != null)
        {
            $oid = $organization->parent_org;
        }

        if ($level == "1") {
            $list = DB::table('organizations')
                ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
                ->where('organizations.id', $oid)
                ->first();

            $class_organization = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
                ->distinct()
                ->where('classes.status', 1)
                ->where('classes.levelid', $level)
                ->where('class_organization.organization_id', $oid)
                ->get();

            // dd($class_organization);

            return response()->json(['data' => $list, 'datayear' => $class_organization]);
        } elseif ($level == "2") {
            $list = DB::table('organizations')
                ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
                ->where('organizations.id', $oid)
                ->first();

            $class_organization = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
                ->distinct()
                ->where('classes.status', 1)
                ->where('classes.levelid', $level)
                ->where('class_organization.organization_id', $oid)
                ->get();

            // dd($class_organization);

            return response()->json(['data' => $list, 'datayear' => $class_organization]);
        }
    }

    public function allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category)
    {
        $organization = Organization::find($oid);
        // dd($organization->parent_org != null ? $organization->parent_org: $oid);
        if ($gender) {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.levelid','>',0)
                ->where('classes.status', "1")
                ->where('students.gender', $gender)  
                ->get();
           
            $data = array(
                'data' => $level,
                'gender' => $gender
            );
        } 
        else 
        {
            if($category == "Kategori Berulang")
            {
                $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id', 'class_student.start_date as class_student_start_date')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.status', "1")
                ->where('class_student.start_date', '<', $date_end)
                ->get();

                $data = array(
                    'data' => $level
                );
            }
            else
            {
                $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.levelid','>',0)
                ->where('classes.status', "1")
                ->get();

                $data = array(
                    'data' => $level
                );
            }
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name'          => $name,
            'desc'          => $desc,
            'category'      => $category,
            'quantity'      => $quantity,
            'price'         => $price,
            'totalAmount'       => $total,
            'start_date'        => $date_started,
            'end_date'          => $date_end,
            'status'            => "1",
            'target'            => $target,
            'organization_id'   => $oid,

        ]);

        for ($i = 0; $i < count($list); $i++) {

            $fees_student = DB::table('class_student')
                ->where('id', $list[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);
            
            // DB::table('student_fees_new')->insert([
            //     'status' => 'Debt',
            //     'fees_id' => $fees,
            //     'class_student_id' => $list[$i]->class_student_id,
            // ]);

            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,
            ]);
            
            if($category == "Kategori Berulang")
            {
                $datestarted = Carbon::parse($date_started); //back to original date without format (string to datetime)
                $dateend = Carbon::parse($date_end);
                $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                $cs_startdate = Carbon::parse($list[$i]->class_student_start_date);
                $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                if($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day)
                {
                    $totalDayLeft = $totalDay;
                }
                $finalAmount = $total * ($totalDayLeft / $totalDay);
                if($finalAmount > $total)
                {
                    $finalAmount = $total;
                }

                DB::table('fees_recurring')->insert([
                    'student_fees_new_id' => $student_fees_new,
                    'totalDay' => $totalDay,
                    'totalDayLeft' => $totalDayLeft,
                    'finalAmount' => $finalAmount,
                    'desc' => 'RM' . $total . '*' . $totalDayLeft . '/' . $totalDay,
                    'created_at' => now(),
                ]);

                //dd($total * ($totalDayLeft / $totalDay));
            }
        }

        // dd($list);

        if ($category == "Kategori B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } 
        else if($category == "Kategori C") {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
        else {
            return redirect('/fees/Recurring')->with('success', 'Yuran Kategori Berulang telah berjaya dimasukkan');
        }
    }

    public function allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category)
    {
        $organization = Organization::find($oid);

        if ($gender) {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.levelid', $level)
                ->where('classes.status', "1")
                ->where('class_student.status',1)
                ->where('students.gender', $gender)
                ->get();
            $data = array(
                'data' => $level,
                'gender' => $gender
            );
        } else {
            if($category == "Kategori Berulang")
            {
                $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id', 'class_student.start_date as class_student_start_date')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.levelid', $level)
                ->where('classes.status', "1")
                ->where('class_student.status',1)

                ->where('class_student.start_date', '<', $date_end)
                ->get();
                $data = array(
                    'data' => $level
                );
            }
            else
            {
                $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.levelid', $level)
                ->where('classes.status', "1")
                ->where('class_student.status',1)
                ->get();
                $data = array(
                    'data' => $level
                );
            }
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name'          => $name,
            'desc'          => $desc,
            'category'      => $category,
            'quantity'      => $quantity,
            'price'         => $price,
            'totalAmount'       => $total,
            'start_date'        => $date_started,
            'end_date'          => $date_end,
            'status'            => "1",
            'target'            => $target,
            'organization_id'   => $oid,

        ]);

        for ($i = 0; $i < count($list); $i++) {

            $fees_student = DB::table('class_student')
                ->where('id', $list[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);

            // DB::table('student_fees_new')->insert([
            //     'status' => 'Debt',
            //     'fees_id' => $fees,
            //     'class_student_id' => $list[$i]->class_student_id,
            // ]);

            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,
            ]);
            
            if($category == "Kategori Berulang")
            {
                $datestarted = Carbon::parse($date_started); //back to original date without format (string to datetime)
                $dateend = Carbon::parse($date_end);
                $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                $cs_startdate = Carbon::parse($list[$i]->class_student_start_date);
                $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                if($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day)
                {
                    $totalDayLeft = $totalDay;
                }
                $finalAmount = $total * ($totalDayLeft / $totalDay);
                if($finalAmount > $total)
                {
                    $finalAmount = $total;
                }

                DB::table('fees_recurring')->insert([
                    'student_fees_new_id' => $student_fees_new,
                    'totalDay' => $totalDay,
                    'totalDayLeft' => $totalDayLeft,
                    'finalAmount' => $finalAmount,
                    'desc' => 'RM' . $total . '*' . $totalDayLeft . '/' . $totalDay,
                    'created_at' => now(),
                ]);
                
                //dd($total * ($totalDayLeft / $totalDay));
            }
        }

        if ($category == "Kategori B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        }
        else if($category == "Kategori C") {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
        else {
            return redirect('/fees/Recurring')->with('success', 'Yuran Kategori Berulang telah berjaya dimasukkan');
        }
    }

    public function allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category)
    {
        // get list class checked from checkbox
        $organization = Organization::find($oid);

        $list = DB::table('classes')
            ->where('status', "1")
            ->whereIn('id', $class)
            ->get();

        // dd(count($list));
        for ($i = 0; $i < count($list); $i++) {
            $class_arr[] = $list[$i]->id;
        }

        if ($gender) {
            $list_student = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.status', "1")
                ->where('class_student.status',1)
                ->where('students.gender', $gender)
                ->whereIn('classes.id', $class)
                ->get();
            $data = array(
                'data' => $class_arr,
                'gender' => $gender
            );
        } else {
            if($category == "Kategori Berulang")
            {
                $list_student = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id', 'class_student.start_date as class_student_start_date')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.status', "1")
                ->where('class_student.status',1)
                ->whereIn('classes.id', $class)
                ->where('class_student.start_date', '<', $date_end)
                ->get();
                $data = array(
                    'data' => $class_arr
                );
            }
            else
            {
                $list_student = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.status', "1")
                ->where('class_student.status',1)
                ->whereIn('classes.id', $class)
                ->get();
                $data = array(
                    'data' => $class_arr
                );
            }
            
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name'              => $name,
            'desc'              => $desc,
            'category'          => $category,
            'quantity'          => $quantity,
            'price'             => $price,
            'totalAmount'       => $total,
            'start_date'        => $date_started,
            'end_date'          => $date_end,
            'status'            => "1",
            'target'            => $target,
            'organization_id'   => $oid,
        ]);
        
        for ($i = 0; $i < count($list_student); $i++) {
            $fees_student = DB::table('class_student')
                ->where('id', $list_student[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);

            // DB::table('student_fees_new')->insert([
            //     'status' => 'Debt',
            //     'fees_id' => $fees,
            //     'class_student_id' => $list_student[$i]->class_student_id,
            // ]);

            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list_student[$i]->class_student_id,
            ]);
            
            if($category == "Kategori Berulang")
            {
                $datestarted = Carbon::parse($date_started); //back to original date without format (string to datetime)
                $dateend = Carbon::parse($date_end);
                $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                $cs_startdate = Carbon::parse($list_student[$i]->class_student_start_date);
                $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                if($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day)
                {
                    $totalDayLeft = $totalDay;
                }
                $finalAmount = $total * ($totalDayLeft / $totalDay);
                if($finalAmount > $total)
                {
                    $finalAmount = $total;
                }

                DB::table('fees_recurring')->insert([
                    'student_fees_new_id' => $student_fees_new,
                    'totalDay' => $totalDay,
                    'totalDayLeft' => $totalDayLeft,
                    'finalAmount' => $finalAmount,
                    'desc' => 'RM' . $total . '*' . $totalDayLeft . '/' . $totalDay,
                    'created_at' => now(),
                ]);
                
                //dd($total * ($totalDayLeft / $totalDay));
            }
        }
        
        if ($category == "Kategori B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } 
        else if($category == "Kategori C") {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
        else {
            return redirect('/fees/Recurring')->with('success', 'Yuran Kategori Berulang telah berjaya dimasukkan');
        }
    }

    public function dependent_fees()
    {
        $userid = Auth::id();

        // ************************* get list dependent from user id  *******************************

        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as nschool', 'organizations.parent_org as parent_org', 'students.id as studentid', 'students.nama as studentname', 'classes.nama as classname','classes.levelid', 'organizations.type_org as type_org', 'class_student.start_date as student_startdate')
            ->where('organization_user.user_id', $userid)
            ->where('class_student.status',1)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->orderBy('organizations.id')
            ->orderBy('classes.nama')
            ->get();
        //dd($list);
        $list_dependent = [];

        foreach ($list as $key => $dependent)
        {
            array_push($list_dependent, $dependent->studentid);
        }
        
        // ************************* get list organization by parent  *******************************

        $organizations = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            //->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            //->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->select('organizations.*', 'organization_user.user_id')
            ->distinct()
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->whereNull('organizations.deleted_at')
            ->orderBy('organizations.nama')
            ->get();

        // dd($organizations);
        // ************************* get list fees  *******************************

        $getfees = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization','class_student.organclass_id','class_organization.id')
            ->join('classes','class_organization.class_id','classes.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.category', 'fees_new.organization_id', 'students.id as studentid','classes.levelid')
            ->distinct()
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('fees_new.status', 1)
            ->where('class_student.status',1)
            ->whereIn('students.id', $list_dependent)
            ->where('student_fees_new.status', 'Debt')
            ->get();
       
        $getfees_bystudent = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            //->join('fees_recurring as fr', 'fr.student_fees_new_id', '=', 'student_fees_new.id')
            ->select('fees_new.*', 'students.id as studentid')
            ->orderBy('fees_new.name')
            ->where('fees_new.status', 1)
            ->where('class_student.status',1)
            ->where('student_fees_new.status', 'Debt')
            ->whereIn('students.id', $list_dependent)
            ->get();

        $getfees_bystudentSwasta = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->join('fees_recurring as fr', 'fr.student_fees_new_id', '=', 'student_fees_new.id')
            ->select('fees_new.*', 'students.id as studentid', 'fr.*', 'fees_new.id as feesnew_id')
            ->orderBy('fees_new.name')
            ->where('class_student.status', 1)
            ->where('fees_new.status', 1)
            ->where('class_student.status',1)
            ->where('student_fees_new.status', 'Debt')
            ->whereIn('students.id', $list_dependent)
            ->get();

        //dd($getfees,$getfees_bystudent);
        // ************************* get fees category A  *******************************
        $getfees_category_A = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.category', 'organization_user.organization_id')
            ->distinct()
            ->orderBy('fees_new.category')
            ->where('fees_new.status', 1)
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Debt')
            ->get();
            
        $getfees_category_A_byparent  = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.*')
            ->orderBy('fees_new.category')
            ->where('fees_new.status', 1)
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Debt')
            ->distinct()
            ->get();
        //dd($list,$organizations,$getfees,$getfees_bystudent,$getfees_category_A,$getfees_category_A_byparent);
        //dd($getfees_category_A_byparent);
        return view('fee.pay.index', compact('list', 'organizations', 'getfees', 'getfees_bystudent', 'getfees_bystudentSwasta', 'getfees_category_A', 'getfees_category_A_byparent'));
    }

    public function student_fees(Request $request)
    {
        $student_id = $request->student_id;
        $getfees_bystudent     = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.*','students.id as studentid', 'students.nama as studentnama', 'student_fees_new.status')
            ->where('students.id', $student_id)
            ->orderBy('fees_new.name')
            ->get();

        return response()->json($getfees_bystudent, 200);
    }

    public function parent_dependent(Request $request)
    {
        $case = explode("-", $request->data);

        $user_id         = $case[0];
        $organization_id = $case[1];

        $get_dependents = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('users', 'users.id', 'organization_user.user_id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.*', 'classes.nama as classname', 'users.name as username')
            ->where('organization_user.user_id', $user_id)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.organization_id', $organization_id)
            ->where('organization_user.status', 1)
            ->where('class_student.status', 1)
            ->get();

        return response()->json($get_dependents, 200);
    }

    public function searchreport()
    {
        $organization = $this->getOrganizationByUserId();

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $organization[0]->id]
            ])
            ->orderBy('classes.nama')
            ->get();

        return view('fee.report-search.index', compact('organization', 'listclass'));
    }

    public function searchreportswasta()
    {
        $organization = $this->getOrganizationByUserId();

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $organization[0]->id]
            ])
            ->orderBy('classes.nama')
            ->get();

        return view('fee.report-search-swasta.index', compact('organization', 'listclass'));
    }

    public function getFeesReceiptDataTable(Request $request){

        if(Auth::user()->hasRole('Superadmin'))
        {
            if($request->oid === NULL)
            {
                $listHisotry = DB::table('transactions as t')
                ->where(function($query) {
                    $query->where('t.description', 'like', 'YS%')
                        ->orWhere('t.nama', 'like', 'School_Fees%');
                })
                    ->where('t.status', 'success')
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ;
            }
            else
            {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where(function($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ;
            }
        }
        else{
            if($request->oid === NULL)
            {
                $listHisotry = DB::table('transactions as t')
                    ->where('t.user_id', Auth::id())
                    ->where(function($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ;
            }
            else if(Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Koop Admin') || Auth::user()->hasRole('Pentadbir Swasta'))
            {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where(function($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ;
            }
            else if(Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Pentadbir Swasta') || Auth::user()->hasRole('Guru Swasta'))
            {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('organization_user', 'co.organ_user_id', 'organization_user.id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where(function($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->where('organization_user.user_id', Auth::id())
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ;
            }
            else
            {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where('t.user_id', Auth::id())
                    ->where(function($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ;
            }
        }

        if($request->start_date != null && $request->end_date != null){
            $listHisotry = $listHisotry->whereBetween('datetime_created',[$request->start_date,$request->end_date]);
            

        }
        $listHisotry = $listHisotry->get();
      //  dd($listHisotry,$request->start_date);
        if (request()->ajax()) {
            return datatables()->of($listHisotry)
                ->editColumn('amount', function ($data) {
                    return number_format($data->amount, 2);
                })
                ->editColumn('date', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->date)->format('d/m/Y');
                    return $formatedDate;
                })
                ->addColumn('action', function ($data) {

                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href=" ' . route('receipttest', $data->id) . ' " class="btn btn-primary m-1" target="_blank">Papar Resit</a></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function cetegoryReportIndex(){

        $organization = $this->getOrganizationByUserId();
        // $student_user = DB::table('students as s')
        // ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
        // ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
        // ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
        // ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
        // ->where('co.class_id', 530)
        // ->select('ou.user_id','s.*')
        // ->orderBy('s.nama')
        // ->get()
        // ->keyBy('user_id');

        // $feeA=DB::table('fees_new_organization_user as fou')
        //         ->leftJoin('organization_user as ou','ou.id','fou.organization_user_id')
        //         ->where('ou.organization_id',159)
        //         ->where('fou.fees_new_id',565)
        //         ->select('ou.user_id','fou.status')
        //         ->get()
        //         ->keyBy('user_id');
        // $data = $student_user->map(function ($student) use ($feeA) {
        //     $user_id = $student->user_id;
        //     if ($feeA->has($user_id)) {
        //         $fee_data = $feeA->get($user_id);
        //         $student->status = $fee_data->status; // Add the status from $feeA to $student_user
        //     }
        //     return $student;
        // });

        
        return view('fee.categoryReport.index', compact('organization'));
    }

    public function cetegoryReportIndexSwasta(){

        $organization = $this->getOrganizationByUserId();
        return view('fee.categoryReport-swasta.index', compact('organization'));
    }

    public function fetchClassForCateYuran(Request $request)
    {

        // dd($request->get('schid'));
        $organ = Organization::find($request->get('oid'));

        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Koop Admin') || Auth::user()->hasRole('Pentadbir Swasta')) {

            $list = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select('classes.id as cid', 'classes.nama as cname')
                ->where([
                    ['class_organization.organization_id', $organ->parent_org != null ?  $organ->parent_org :  $organ->id],
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
                    ['class_organization.organization_id', $organ->parent_org != null ?  $organ->parent_org :  $organ->id],
                    ['classes.status', 1],
                    ['organization_user.user_id', Auth::id()]
                ])
                ->orderBy('classes.nama')
                ->get();
        }

        $years = DB::table('fees_new')
        ->where('organization_id',$organ->id)
        ->selectRaw('DISTINCT YEAR(start_date) as year')
        ->orderByDesc('year')
        ->get();

        return response()->json(['success' => $list,'years'=>$years]);
    }

    public function fetchYuran(Request $request)
    {
        $class = ClassModel::find($request->classid);
        $oid = $request->oid;
        $year = $request->fee_year;
        $lists = DB::table('fees_new')
        ->select('fees_new.*', DB::raw("CONCAT(fees_new.category, ' - ', fees_new.name) AS name"))
        ->where('organization_id', $oid)
        ->whereYear('start_date',$year)
        ->orderBy('category')
        ->orderBy('name')
        ->get();

         //dd($lists,$year);

        foreach($lists as $key=>$list)
        {
            $target = json_decode($list->target);
            // dd($target->data);

            if($target->data == "All_Level" || $target->data == "ALL" || $target->data == $class->levelid)
            {
                continue;
            }

            if(is_array($target->data))
            {
                if(in_array($class->id, $target->data))
                {
                    continue;
                }
            }

            unset($lists[$key]);
        }

        return response()->json(['success' => $lists]);
    }

    public function fecthYuranByOrganizationId(Request $request)
    {
        $oid = $request->oid;
        $year = $request->fee_year;
        $yurans = DB::table('fees_new')
            ->where('organization_id', $oid)
            ->whereYear('start_date',$year)
            ->select('id', DB::raw("CONCAT(fees_new.category, ' - ', fees_new.name) AS name"))
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json(['success' => $yurans]);
    }

    public function studentDebtDatatable(Request $request)
    {
        $fees = Fee_New::find($request->feeid);

        if (request()->ajax()) {
            if($fees->category == "Kategori A")
            {
                $student_user = DB::table('students as s')
                ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
                ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                ->where('co.class_id', $request->classid)
                ->select('ou.user_id','s.*')
                ->orderBy('s.nama')
                ->get()
                ->keyBy('user_id');
    
                $feeA=DB::table('fees_new_organization_user as fou')
                        ->leftJoin('organization_user as ou','ou.id','fou.organization_user_id')
                        ->where('ou.organization_id',$request->orgId)
                        ->where('fou.fees_new_id',$request->feeid)
                        ->select('ou.user_id','fou.status')
                        ->get()
                        ->keyBy('user_id');
                $data = $student_user->map(function ($student) use ($feeA) {
                    $user_id = $student->user_id;
                    if ($feeA->has($user_id)) {
                        $fee_data = $feeA->get($user_id);
                        $student->status = $fee_data->status; // Add the status from $feeA to $student_user
                    }
                    return $student;
                });
                
            }
            else
            {
                if($fees->category != "Kategori Berulang")
                {
                    $data = DB::table('students as s')
                        ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                        ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                        ->leftJoin('student_fees_new as sfn', 'sfn.class_student_id', 'cs.id')
                        ->where('sfn.fees_id', $fees->id)
                        ->where('co.class_id', $request->classid)
                        ->select('s.*', 'sfn.status')
                        ->orderBy('s.nama')
                        ->get();
                }
                else
                {
                    $data = DB::table('students as s')
                    ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                    ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->leftJoin('student_fees_new as sfn', 'sfn.class_student_id', 'cs.id')
                    ->leftJoin('fees_recurring as fr', 'fr.student_fees_new_id', 'sfn.id')
                    ->where('sfn.fees_id', $fees->id)
                    ->where('co.class_id', $request->classid)
                    ->where('cs.status', 1)
                    ->select('s.*', 'sfn.status', 'cs.start_date as cs_startdate', 'fr.*')
                    ->orderBy('s.nama')
                    ->get();
                }
                
            }

            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if (property_exists($row, 'status')) {
                    if($row->status == 'Debt')
                    {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Masih Berhutang </span></div>';

                        return $btn;
                    }
                    else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Telah Bayar </span></div>';
    
                        return $btn;
                    }
                    
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Masih Berhutang </span></div>';

                    return $btn;
                }
            });


            $table->rawColumns(['status']);

            return $table->make(true);
        }
    }

    public function ExportAllYuranStatus(Request $request)
    {
       
        if($request->yuranExport==0){
            $yuran = DB::table('fees_new')
            ->where('organization_id', $request->organExport)
            ->get();
            $filename = "LaporanSemuaYuran";

        }
        else{
            $yuran = DB::table('fees_new')
            ->where('id', $request->yuranExport)
            ->get();
            $filename = str_replace('/','-',$yuran[0]->name);

        }
        

        $orgtypeSwasta = DB::table('organizations as o')
            ->where('id', $request->organExport)
            ->where('o.type_org', 15)
            ->get();

       
        if(!$orgtypeSwasta || count($orgtypeSwasta)==0)
        {
            return Excel::download(new ExportYuranStatus($yuran),  $filename. '.xlsx');
        }
        else
        {
            return Excel::download(new ExportYuranStatusSwasta($yuran), $filename . '.xlsx');
        }
        
    }

    public function ExportJumlahBayaranIbuBapa(Request $request)
    {
        $org=DB::table('organizations')
        ->where('id',$request->organExport1)
        ->first();

       if($request->yuranExport1!=0){   
        $kelas= DB::table('classes')
        ->where('id', $request->yuranExport1)
        ->first();
        $filename=$kelas->nama;
       }else{
        $filename=$org->nama;
       }

        $filename = str_replace(['/', '\\'], '', $filename);
        $kelasId=$request->yuranExport1;

        $orgtypeSwasta = DB::table('organizations as o')
            ->where('id', $request->organExport1)
            ->where('o.type_org', 15)
            ->get();

            $start_date = $request->date_started;
            $end_date = $request ->date_end;
        if(!$orgtypeSwasta || count($orgtypeSwasta)==0)
        {
            return Excel::download(new ExportJumlahBayaranIbuBapa($request->yuranExport1,$org,$start_date,$end_date ), $filename . '.xlsx');
        }
        else
        {
            return Excel::download(new ExportJumlahBayaranIbuBapaSwasta($request->yuranExport1,$org ), $filename . '.xlsx');
        }
    }

    public function exportYuranOverview(Request $request){
        
        $org=DB::table('organizations')
        ->where('id',$request->organization)
        ->first();
        //dd($request);
        $filename="Yuran_Overview_".$org->nama;
        return Excel::download(new ExportYuranOverview($org->id ), $filename . '.xlsx');
    }
}


// set_time_limit(500);
//         $users=DB::table('organization_user as ou')
//                 ->leftJoin('organization_user_student as ous', 'ous.organization_user_id', 'ou.id')
//                 ->leftJoin('students as s', 's.id', 'ous.student_id')
//                 ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
//                 ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
//                 ->leftJoin('classes as c','c.id','co.class_id')
//                 ->leftJoin('users as u','u.id','ou.user_id')
//                 ->whereIn('c.id',[538,539,540])

//                 ->select('u.*')
//                 ->get();
//         //dd($users);
//         foreach($users as $u){

//             DB::table('users')
            
//             ->where('id', $u->id)
//             ->update([
//                 'name'=> preg_replace('/^\s+|\s+$/u', '', $u->name)
//             ]);
//         }
//         dd("success");