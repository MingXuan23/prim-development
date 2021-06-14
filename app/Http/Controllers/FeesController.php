<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeesController extends Controller
{
    public function index()
    {
        //
        $fees = DB::table('fees')->orderBy('nama')->get();
        return view('pentadbir.fee.index', compact('fees'));
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
        return view('parent.fee.index', compact('list', 'getfees', 'getcat', 'getdetail'));
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        return view('pentadbir.fee.add', compact('organization'));
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'         =>  'required',
            'year'         =>  'required',
            // 'cat'          =>  'required',
        ]);

        $yearstd = $request->get('year');

        // get type sekolah jaim
        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('class_organization.id as id')
            ->orderBy('nama')
            ->where('classes.nama', 'LIKE',  '%' . $yearstd . '%')
            ->get();

        // dd($listclass[0]->id);

        $date = Carbon::now();
        $timestemp = $date->toDateTimeString();
        $year = Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;
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

            for ($i = 0; $i < count($listclass); $i++) {
                $array[] = array(
                    'class_organization_id' => $listclass[$i]->id,
                    'fees_id' => $fee->id,
                    'status' => 'active'
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

            for ($i = 0; $i < count($listclass); $i++) {
                $array[] = array(
                    'class_organization_id' => $listclass[$i]->id,
                    'fees_id' => $fee->id,
                    'status' => 'active'
                );
            }

            DB::table('class_fees')->insert($array);
        }

        return redirect('/fees')->with('success', 'New fees has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
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
                $query->where('user_id', $userId);
            })->get();
        }
    }

    public function fetchYear(Request $request)
    {

        // dd($request->get('schid'));
        $oid = $request->get('oid');

        // $list = DB::table('organizations')
        //     ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
        //     ->join('classes', 'classes.id', '=', 'class_organization.class_id')
        //     ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname')
        //     ->where('organizations.id', $oid)
        //     ->get();

        $list = DB::table('organizations')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
            ->where('organizations.id', $oid)
            ->first();



        // dd($list);
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
            ->get();

        // $list = DB::table('organizations')
        //     ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
        //     ->where('organizations.id', $oid)
        //     ->first();



        // dd($list);
        return response()->json(['success' => $list]);
    }

}
