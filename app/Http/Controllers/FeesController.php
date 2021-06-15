<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Psy\Command\WhereamiCommand;

class FeesController extends Controller
{
    public function index()
    {
        //
        $fees = DB::table('fees')->orderBy('nama')->get();
        $organization = $this->getOrganizationByUserId();

        return view('pentadbir.fee.index', compact('fees', 'organization'));
    }

    public function parentpay()
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

        return view('parent.fee.index', compact('list', 'getfees', 'getcat', 'getdetail', 'organization'));
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        return view('pentadbir.fee.add', compact('organization'));
    }

    public function store(Request $request)
    {
        //class return array
        $class = $request->get('cb_class');

        // dd($aa[0]);

        $this->validate($request, [
            'name'              =>  'required',
            'year'              =>  'required',
            'cb_class'          =>  'required',
            'organization'      =>  'required',
            // 'cat'          =>  'required',
        ]);

        $yearstd = $request->get('year');
        $year   = $request->get('year');
        $oid    = $request->get('organization');

        // get list class checked from checkbox
        $list = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname', 'class_organization.id as co_id')
            ->where('organizations.id', $oid)
            ->whereIn('classes.id',  $class)
            ->get();

        // dd($list);

        $date       = Carbon::now();
        $timestemp  = $date->toDateTimeString();
        $year       = Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;
        // dd($year);

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
                'nama'     =>  $request->get('name'),
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
        } else {

            $fee = new Fee([
                'nama'     =>  $request->get('name'),
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
        }

        return redirect('/fees')->with('success', 'Yuran telah berjaya dimasukkan');
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
        } else {
            // user role guru 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('role_id', '6')->OrWhere('role_id', '7')->OrWhere('role_id', '8');
            })->get();
        }
    }

    public function fetchYear(Request $request)
    {

        // dd($request->get('schid'));
        $oid = $request->get('oid');

        $list = DB::table('organizations')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
            ->where('organizations.id', $oid)
            ->first();

        return response()->json(['success' => $list]);
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
                $btn = $btn . '<a href="' . route('fees.edit', $row->feeid) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->feeid . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }
}
