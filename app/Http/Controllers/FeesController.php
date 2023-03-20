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
use App\Exports\ExportJumlahBayaranIbuBapa;
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
        } elseif (Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Koop Admin')) {

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
                ->whereIn('ou.role_id', [4, 5 ,12])
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
            ->where('classes.nama', 'LIKE', '%' . $year . '%')
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
        $oid = $request->oid;

        $all_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', $oid)
            ->count();

        // dd($all_student);
        $student_complete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.fees_status', 'Completed')
            ->count();

        $student_notcomplete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.fees_status', 'Not Complete')
            ->count();

        $all_parent =  DB::table('organization_user')
            ->where('organization_id', $oid)
            ->where('role_id', 6)
            ->where('status', 1)
            ->count();

        $parent_complete =  DB::table('organization_user')
            ->where('organization_id', $oid)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Completed')
            ->count();

        $parent_notcomplete =  DB::table('organization_user')
            ->where('organization_id', $oid)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Not Complete')
            ->count();

        return response()->json(['all_student' => $all_student, 'student_complete' => $student_complete, 'student_notcomplete' => $student_notcomplete, 'all_parent' => $all_parent, 'parent_complete' => $parent_complete, 'parent_notcomplete' => $parent_notcomplete], 200);

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

                $data = DB::table('users')
                    ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                    ->select('users.*', 'organization_user.organization_id')
                    ->where('organization_user.organization_id', $oid)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('organization_user.fees_status', 'Completed')
                    ->get();
            } else {
                $data = DB::table('users')
                    ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                    ->select('users.*', 'organization_user.organization_id')
                    ->where('organization_user.organization_id', $oid)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('organization_user.fees_status', 'Not Complete')
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
            'category'          =>  "Kategory A",
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
            $parent_id = DB::table('organization_user')
                ->where('organization_id', $oid)
                ->where('role_id', 6)
                ->where('status', 1)
                ->get();

            // to make sure one parent would recieve one only katagory fee if he or she hv more than children in school
            for ($i = 0; $i < count($parent_id); $i++) {
                $fees_parent = DB::table('organization_user')
                    ->where('id', $parent_id[$i]->id)
                    ->update(['fees_status' => 'Not Complete']);

                DB::table('fees_new_organization_user')->insert([
                    'status' => 'Debt',
                    'fees_new_id' => $fee->id,
                    'organization_user_id' => $parent_id[$i]->id,
                ]);
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
                        ->where('category', "Kategory A")
                        ->where('status', "1")
                        ->get();
                    
                    foreach($data as $d)
                    {
                        $d->target = "Setiap Keluarga";
                    }

                } elseif ($category == "B") {
                    $data     = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategory B")
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
                        ->where('category', "Kategory C")
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
        $category       = "Kategory B";

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
        $category       = "Kategory C";

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
                ->where('classes.status', "1")
                ->where('students.gender', $gender)
                ->get();

            $data = array(
                'data' => $level,
                'gender' => $gender
            );
        } else {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.status', "1")
                ->get();

            $data = array(
                'data' => $level
            );
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
            
            DB::table('student_fees_new')->insert([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,
            ]);
        }

        if ($category == "Kategory B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
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
                ->where('students.gender', $gender)
                ->get();
            $data = array(
                'data' => $level,
                'gender' => $gender
            );
        } else {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.levelid', $level)
                ->where('classes.status', "1")
                ->get();
            $data = array(
                'data' => $level
            );
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

            DB::table('student_fees_new')->insert([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,
            ]);
        }

        if ($category == "Kategory B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
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
                ->where('students.gender', $gender)
                ->whereIn('classes.id', $class)
                ->get();
            $data = array(
                'data' => $class_arr,
                'gender' => $gender
            );
        } else {
            $list_student = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org: $oid)
                ->where('classes.status', "1")
                ->whereIn('classes.id', $class)
                ->get();
            $data = array(
                'data' => $class_arr
            );
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

            DB::table('student_fees_new')->insert([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list_student[$i]->class_student_id,
            ]);
        }
        
        if ($category == "Kategory B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
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
            ->select('organizations.id as oid', 'organizations.nama as nschool', 'organizations.parent_org as parent_org', 'students.id as studentid', 'students.nama as studentname', 'classes.nama as classname')
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->orderBy('organizations.id')
            ->orderBy('classes.nama')
            ->get();

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
            ->orderBy('organizations.nama')
            ->get();

        // dd($organizations);
        // ************************* get list fees  *******************************

        $getfees = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.category', 'fees_new.organization_id', 'students.id as studentid')
            ->distinct()
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('fees_new.status', 1)
            ->whereIn('students.id', $list_dependent)
            ->where('student_fees_new.status', 'Debt')
            ->get();
        
        $getfees_bystudent = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.*', 'students.id as studentid')
            ->orderBy('fees_new.name')
            ->where('fees_new.status', 1)
            ->where('student_fees_new.status', 'Debt')
            ->whereIn('students.id', $list_dependent)
            ->get();

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
            ->get();

        return view('fee.pay.index', compact('list', 'organizations', 'getfees', 'getfees_bystudent', 'getfees_category_A', 'getfees_category_A_byparent'));
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

    public function getFeesReceiptDataTable(Request $request){

        if(Auth::user()->hasRole('Superadmin'))
        {
            if($request->oid === NULL)
            {
                $listHisotry = DB::table('transactions as t')
                    ->where('t.description', "like", 'YS%')
                    ->where('t.status', 'success')
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->get();
            }
            else
            {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where('t.description', "like", 'YS%')
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ->get();
            }
        }
        else{
            if($request->oid === NULL)
            {
                $listHisotry = DB::table('transactions as t')
                    ->where('t.user_id', Auth::id())
                    ->where('t.description', "like", 'YS%')
                    ->where('t.status', 'success')
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->get();
            }
            else if(Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Koop Admin'))
            {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where('t.description', "like", 'YS%')
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ->get();
            }
            else if(Auth::user()->hasRole('Guru'))
            {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('organization_user', 'co.organ_user_id', 'organization_user.id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where('t.description', "like", 'YS%')
                    ->where('t.status', 'success')
                    ->where('organization_user.user_id', Auth::id())
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ->get();
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
                    ->where('t.description', "like", 'YS%')
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ->get();
            }
        }

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

        return view('fee.categoryReport.index', compact('organization'));
    }

    public function fetchClassForCateYuran(Request $request)
    {

        // dd($request->get('schid'));
        $organ = Organization::find($request->get('oid'));

        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Koop Admin')) {

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

        return response()->json(['success' => $list]);
    }

    public function fetchYuran(Request $request)
    {
        $class = ClassModel::find($request->classid);
        $oid = $request->oid;

        $lists = DB::table('fees_new')
        ->select('fees_new.*', DB::raw("CONCAT(fees_new.category, ' - ', fees_new.name) AS name"))
        ->where('organization_id', $oid)
        ->orderBy('category')
        ->orderBy('name')
        ->get();

        // dd($lists);

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

        $yurans = DB::table('fees_new')
            ->where('organization_id', $oid)
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
            if($fees->category == "Kategory A")
            {
                $data = DB::table('students as s')
                    ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                    ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
                    ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                    ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->leftJoin('fees_new_organization_user as fou', 'fou.organization_user_id', 'ou.id')
                    ->where('fou.fees_new_id', $fees->id)
                    ->where('co.class_id', $request->classid)
                    ->select('s.*', 'fou.status')
                    ->orderBy('s.nama')
                    ->get();
            }
            else
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

            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if ($row->status == 'Debt') {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Masih Berhutang </span></div>';

                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-success"> Telah Bayar </span></div>';

                    return $btn;
                }
            });


            $table->rawColumns(['status']);

            return $table->make(true);
        }
    }

    public function ExportAllYuranStatus(Request $request)
    {
        $yuran = DB::table('fees_new')
            ->where('id', $request->yuranExport)
            ->first();

        return Excel::download(new ExportYuranStatus($yuran), $yuran->name . '.xlsx');
    }

    public function ExportJumlahBayaranIbuBapa(Request $request)
    {
        $kelas = DB::table('classes')
            ->where('id', $request->yuranExport1)
            ->first();

        return Excel::download(new ExportJumlahBayaranIbuBapa($kelas, $request->organExport1), $kelas->nama . '.xlsx');
    }
}
