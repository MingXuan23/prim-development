<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Organization;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Psy\Command\WhereamiCommand;
use App\Http\Controllers\AppBaseController;
use App\Models\Category;

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

    public function parentpay()
    {
        $userid = Auth::id();

        // ************************* get list dependent from user id  *******************************

        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('class_fees', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('fees', 'class_fees.fees_id', '=', 'fees.id')
            ->select('organizations.id as oid', 'organizations.nama as nschool', 'students.id as studentid', 'students.nama as studentname', 'classes.nama as classname', 'organization_roles.nama as rolename', 'fees.id as feeid', 'fees.nama as feename')
            ->where([
                ['users.id', $userid],
            ])
            ->where(function ($query) {
                $query->where('organization_roles.id', '=', 6)
                    ->orWhere('organization_roles.id', '=', 7)
                    ->orWhere('organization_roles.id', '=', 8);
            })
            ->orderBy('classes.nama')
            ->get();


        $feesid     = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('class_student', 'class_organization.class_id', '=', 'class_student.id')
            ->join('students', 'class_student.student_id', '=', 'students.id')
            ->select('fees.id as feeid', 'students.id as studentid')
            ->first();

        // dd($feesid);

        $getfees    = DB::table('fees')->where('id', $feesid->feeid)->first();


        // ************************* get list category  *******************************

        $getcat = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->distinct('categories.nama')
            ->select('fees.id as feeid', 'categories.id as cid', 'categories.nama as cnama')
            ->orderBy('categories.id')
            ->get();

        // ************************* get details of fee  *******************************
        // join table student fees where debt
        $getdetail  = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->select('fees.id as feeid', 'categories.id as cid', 'categories.nama as cnama', 'details.nama as dnama', 'details.quantity as quantity', 'details.price as price', 'details.totalamount as totalamount', 'details.id as did')

            ->orderBy('details.nama')
            ->get();
        // return view('pentadbir.fee.pay', compact('getfees', 'getcat', 'getdetail'));

        // dd($list);

        // $fees = DB::table('fees')->orderBy('nama')->get();
        $organization = $this->getOrganizationByUserId();

        return view('parent.fee.index', compact('list', 'getfees', 'getcat', 'getdetail', 'organization'));
    }

    //DEVELOPMENT CONTROLLER
    public function devpay()
    {
        $userid = Auth::id();
        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('class_fees', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('fees', 'class_fees.fees_id', '=', 'fees.id')
            ->select('organizations.id as oid', 'organizations.nama as nschool', 'students.id as studentid', 'students.nama as studentname', 'classes.nama as classname', 'organization_roles.nama as rolename', 'fees.id as feeid', 'fees.nama as feename')
            ->where([
                ['users.id', $userid],
            ])
            ->orWhere('organization_roles.id', '=', 6)
            ->orWhere('organization_roles.id', '=', 7)
            ->orWhere('organization_roles.id', '=', 8)
            ->orderBy('classes.nama')
            ->get();

        $feesid     = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('class_student', 'class_organization.class_id', '=', 'class_student.id')
            ->join('students', 'class_student.student_id', '=', 'students.id')
            ->select('fees.id as feeid', 'students.id as studentid')
            ->first();

        // dd($feesid);

        $getfees    = DB::table('fees')->where('id', $feesid->feeid)->first();

        $getcat = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->distinct('categories.nama')
            ->select('fees.id as feeid', 'categories.id as cid', 'categories.nama as cnama')
            ->orderBy('categories.id')
            ->get();

        $getdetail  = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->select('fees.id as feeid', 'categories.id as cid', 'categories.nama as cnama', 'details.nama as dnama', 'details.quantity as quantity', 'details.price as price', 'details.totalamount as totalamount', 'details.id as did')
            ->orderBy('details.nama')
            ->get();
        // return view('pentadbir.fee.pay', compact('getfees', 'getcat', 'getdetail'));

        // dd($list);

        // $fees = DB::table('fees')->orderBy('nama')->get();
        $organization = $this->getOrganizationByUserId();

        return view('parent.dev.index', compact('list', 'getfees', 'getcat', 'getdetail', 'organization'));
    }

    public function devreceipt(Request $request)
    {

        $feesid = $request->feesid;
        $amount = $request->amount;

        $userid = Auth::id();
        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('class_fees', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('fees', 'class_fees.fees_id', '=', 'fees.id')
            ->select('organizations.id as oid', 'organizations.nama as nschool', 'students.id as studentid', 'students.nama as studentname', 'classes.nama as classname', 'organization_roles.nama as rolename', 'fees.id as feeid', 'fees.nama as feename')
            ->where([
                ['users.id', $userid],
            ])
            ->orWhere('organization_roles.id', '=', 6)
            ->orWhere('organization_roles.id', '=', 7)
            ->orWhere('organization_roles.id', '=', 8)
            ->orderBy('classes.nama')
            ->get();

        $feesid     = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('class_student', 'class_organization.class_id', '=', 'class_student.id')
            ->join('students', 'class_student.student_id', '=', 'students.id')
            ->select('fees.id as feeid', 'students.id as studentid')
            ->first();

        return view('parent.dev.receipt', compact('feesid'));
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        return view('pentadbir.fee.add', compact('organization'));
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'outer-group.*.inner-group' =>  'required'
            ],
            ['outer-group.*.inner-group.required' => 'Sila pilih kategori']
        );

        $category   = $request->get("outer-group")[0]["inner-group"];
        $feename    = $request->get("outer-group")[0]["name"];
        // dd($request);
        //class return array
        $class      = $request->get('cb_class');
        // $details    = $request->get('cb_details');

        $oid        = $request->get("outer-group")[0]["organization"];

        $date       = Carbon::now();
        $timestemp  = $date->toDateTimeString();
        $year       = Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;





        // get list class checked from checkbox
        $list = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname', 'class_organization.id as co_id')
            ->where('organizations.id', $oid)
            ->whereIn('classes.id',  $class)
            ->get();

        // dd($list);
        $yearfees = DB::table('year_fees')
            ->select('id')
            ->where('nama', $year)
            ->first();

        if ($yearfees == '') {

            $tahun = DB::table('year_fees')->insertGetId([
                'nama'   => $year
            ]);
            // dd($tahun);

            $fee = new Fee([
                'nama'     =>  $feename,
                'status'   =>  1,
                'yearfees_id' => $tahun
            ]);

            $fee->save();

            for ($i = 0; $i < count($list); $i++) {
                $array[] = array(
                    'class_organization_id' => $list[$i]->co_id,
                    'fees_id' => $fee->id,
                    'status' => '1'
                );
            }
            DB::table('class_fees')->insert($array);

            $this->feesDetails($fee->id, $category);
        } else {

            $fee = new Fee([
                'nama'     =>  $feename,
                'status'   =>  1,
                'yearfees_id' => $yearfees->id

            ]);
            $fee->save();

            for ($i = 0; $i < count($list); $i++) {
                $array[] = array(
                    'class_organization_id' => $list[$i]->co_id,
                    'fees_id' => $fee->id,
                    'status' => '1'
                );
            }

            DB::table('class_fees')->insert($array);

            $this->feesDetails($fee->id, $category);
        }

        return redirect('/fees')->with('success', 'Yuran telah berjaya dimasukkan');
    }

    public function feesDetails($id, $category)
    {
        //********************************************* ***********************************************/

        // dd($category[0]["category"]);
        $category_array = [];

        for ($i = 0; $i < count($category); $i++) {
            // $category_array[] = array('category_id' => $category[$i]["category"]);
            $category_array[] = $category[$i]["category"];
        }

        // dd($category_array);

        // get list details checked from checkbox
        $listdetails = DB::table('details')
            ->whereIn('category_id', $category_array)
            ->get();

        // dd($listdetails);


        for ($i = 0; $i < count($listdetails); $i++) {
            $array[] = array(
                'status'     => 1,
                'details_id' => $listdetails[$i]->id,
                'fees_id'    => $id
            );
        }
        // dd($array);

        DB::table('fees_details')->insert($array);


        //all details in fees
        $fdid = DB::table('fees_details')->where('fees_id', $id)->get();

        // // get student first from the fees
        $liststd =  DB::table('class_student')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('class_fees', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->select('class_student.id as cid')
            ->where('class_fees.fees_id', $id)
            ->get();

        // dd($fdid);

        // //store all student that have fees (req->id) 
        for ($i = 0; $i < count($liststd); $i++) {

            for ($j = 0; $j < count($fdid); $j++) {
                $arraystudent[] = array(
                    'status'            => 'Debt', // berhutang
                    'class_student_id'  => $liststd[$i]->cid,
                    'fees_details_id'   => $fdid[$j]->id
                );
            }
        }

        DB::table('student_fees')->insert($arraystudent);

        // // sum values
        $getsum  = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->where('fees.id', $id)
            ->sum('details.totalamount');

        // dd($getsum);
        // update total amount

        $fees = DB::table('fees')
            ->where('id', $id)
            ->update(['totalamount' => $getsum]);

        //********************************************* ***********************************************/
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
            ->where('classes.nama', 'LIKE',  '%' . $getyear . '%')
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
        //
        // dd($request);
        //class return array
        $class = $request->get('cb_class');

        $req = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname', 'class_organization.id as co_id')
            ->where('organizations.id', $request->get('organization'))
            ->whereIn('classes.id',  $class)
            ->get()->toArray();

        // $getclassfees = DB::table('class_fees')->where('class_organization_id', $list->co_id->array())->get();
        // $arr = $req->toArray();
        // dd(count($req));
        // dd($req[0]);

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
        //
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else if (Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Guru')) {

            // user role pentadbir n guru 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5);
                });
            })->get();
        } else {
            // user role ibu bapa
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('role_id', '6')->OrWhere('role_id', '7')->OrWhere('role_id', '8');
            })->get();
        }
    }

    public function fetchYear(Request $request)
    {

        // dd($request->get('schid'));
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

        $list = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname')
            ->where('organizations.id', $oid)
            ->where('classes.nama', 'LIKE',  '%' . $year . '%')
            ->orderBy('classes.nama')
            ->get();

        return response()->json(['success' => $list]);
    }

    public function getFeesDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $oid = $request->oid;
            $userId = Auth::id();

            if ($oid != '') {

                // $data = DB::table('fees')->orderBy('nama')->get();
                $data     = DB::table('fees')
                    ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
                    ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
                    ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount')
                    ->where('class_organization.organization_id', $oid)
                    ->distinct()
                    ->get();

                // dd($data);
            }
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('details.getfees', $row->feeid) . '" class="btn btn-primary m-1">Butiran</a>';
                // $btn = $btn . '<a href="' . route('fees.edit', $row->feeid) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->feeid . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function feesReport()
    {
        $organizations = $this->getOrganizationByUserId();

        return view('parent.fee.report', compact('organizations'));
    }

    public function getLatestTransaction(Request $request)
    {
        $organizationID = $request->id;

        try {
            $response = Transaction::getLastestTransaction_fees($organizationID);

            // dd($response);
            if (request()->ajax()) {
                return datatables()->of($response)
                    ->editColumn('latest', function ($response) {
                        //change over here
                        return date('d/m/Y', strtotime($response->latest));
                    })
                    ->make(true);
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getTransactionByOrganizationIdAndStatus(Request $request)
    {
        $organizationID = $request->id;
        try {
            $response = Transaction::getTransaction_fees($organizationID);
            // dd($response);

            return $this->sendResponse($response, "Success");
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getTotalCatB(Request $request)
    {
        $organizationID = $request->id;
        $duration = $request->duration;

        if ($duration == "day") {
            try {
                $response = Transaction::getTotalDonationByDay_CatB($organizationID);
                $val = $response->getData()->donation_amount;
                $response = json_decode($response, true);
                $response['donation_amount'] = $val;
                $response['duration'] = 'day';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "week") {
            try {
                $response = Transaction::getTotalDonationByWeek_CatB($organizationID);
                $val = $response->getData()->donation_amount;
                $response = json_decode($response, true);
                $response['donation_amount'] = $val;
                $response['duration'] = 'week';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "month") {
            try {
                $response = Transaction::getTotalDonationByMonth_CatB($organizationID);
                $val = $response->getData()->donation_amount;
                $response = json_decode($response, true);
                $response['donation_amount'] = $val;
                $response['duration'] = 'month';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        }
    }

    public function getTotalCatA(Request $request)
    {
        $organizationID = $request->id;
        $duration = $request->duration;

        if ($duration == "day") {
            try {
                $response = Transaction::getTotalDonorByDay_CatA($organizationID);
                // dd($response->getData()->donor);
                $val = $response->getData()->donor;
                $response = json_decode($response, true);
                $response['donor'] = $val;
                $response['duration'] = 'day';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "week") {
            try {
                $response = Transaction::getTotalDonorByWeek_CatA($organizationID);
                $val = $response->getData()->donor;
                $response = json_decode($response, true);
                $response['donor'] = $val;
                $response['duration'] = 'week';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "month") {
            try {
                $response = Transaction::getTotalDonorByMonth_CatA($organizationID);
                $val = $response->getData()->donor;
                $response = json_decode($response, true);
                $response['donor'] = $val;
                $response['duration'] = 'month';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
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
        $organization = $this->getOrganizationByUserId();
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
        // dd($request->toArray());
        $gender      = "";
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
        $category       = "Category B";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else if ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }



    public function getCategoryBDatatable(Request $request)
    {
        if (request()->ajax()) {
            $oid = $request->oid;
            $userId = Auth::id();

            if ($oid != '') {

                // $data = DB::table('fees')->orderBy('nama')->get();
                $data     = DB::table('fees_new')
                    ->where('organization_id', $oid)
                    ->where('category', "Category B")
                    ->where('status', "1")
                    ->get();

                // dd($data);
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

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                // $btn = $btn . '<a href="' . route('fees.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                // $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['status', 'action']);
            return $table->make(true);
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
        $category       = "Category C";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else if ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function getCategoryCDatatable(Request $request)
    {
        if (request()->ajax()) {
            $oid = $request->oid;
            $userId = Auth::id();

            if ($oid != '') {

                // $data = DB::table('fees')->orderBy('nama')->get();
                $data     = DB::table('fees_new')
                    ->where('organization_id', $oid)
                    ->where('category', "Category C")
                    ->where('status', "1")
                    ->get();

                // dd($data);
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

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                // $btn = $btn . '<a href="' . route('fees.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                // $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['status', 'action']);
            return $table->make(true);
        }
    }

    public function fetchClassYear(Request $request)
    {

        // dd($request->get('level'));
        $level = $request->get('level');
        $oid = $request->get('oid');
        if ($level == "1") {
            $list = DB::table('organizations')
                ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
                ->where('organizations.id', $oid)
                ->first();

            $class_organization = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
                ->distinct()
                ->where('classes.levelid', $level)
                ->where('class_organization.organization_id', $oid)
                ->get();

            // dd($class_organization);

            return response()->json(['data' => $list, 'datayear' => $class_organization]);
        } else if ($level == "2") {

            $list = DB::table('organizations')
                ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
                ->where('organizations.id', $oid)
                ->first();

            $class_organization = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
                ->distinct()
                ->where('classes.levelid', $level)
                ->where('class_organization.organization_id', $oid)
                ->get();

            // dd($class_organization);

            return response()->json(['data' => $list, 'datayear' => $class_organization]);
        }
    }

    public function allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category)
    {
        if ($gender) {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
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
                ->where('class_organization.organization_id', $oid)
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
            $array[] = array(
                'status' => '1',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,

            );
        }
        DB::table('student_fees_new')->insert($array);
        if ($category == "Category B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
    }

    public function allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category)
    {
        if ($gender) {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
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
                ->where('class_organization.organization_id', $oid)
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
            $array[] = array(
                'status' => '1',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,

            );
        }
        DB::table('student_fees_new')->insert($array);
        if ($category == "Category B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
    }

    public function allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category)
    {
        // get list class checked from checkbox

        $list = DB::table('classes')
            ->where('status', "1")
            ->whereIn('id',  $class)
            ->get();

        // dd(count($list));
        for ($i = 0; $i < count($list); $i++) {
            $class_arr[] = $list[$i]->nama;
        }

        if ($gender) {
            $list_student = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
                ->where('classes.status', "1")
                ->where('students.gender', $gender)
                ->whereIn('classes.id',  $class)
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
                ->where('class_organization.organization_id', $oid)
                ->where('classes.status', "1")
                ->whereIn('classes.id',  $class)
                ->get();
            $data = array(
                'data' => $class_arr
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

        for ($i = 0; $i < count($list_student); $i++) {
            $array[] = array(
                'status' => '1',
                'fees_id' => $fees,
                'class_student_id' => $list_student[$i]->class_student_id,

            );
        }
        DB::table('student_fees_new')->insert($array);
        if ($category == "Category B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
    }
}
