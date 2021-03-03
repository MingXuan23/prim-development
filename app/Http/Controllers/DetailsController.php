<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailsController extends Controller
{
    public function index()
    {
        //
        // return view('pentadbir.fee.details.index');
    }

    public function getFees(Request $request)
    {
        //
        $feesid     = $request->id;
        $getfees    = DB::table('fees')->where('id', $feesid)->first();

        $getcat = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->distinct('categories.nama')
            ->select('categories.id as cid', 'categories.nama as cnama')
            ->where('fees.id', $feesid)
            ->orderBy('categories.id')
            ->get();

        $getdetail  = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->select('categories.id as cid', 'categories.nama as cnama', 'details.nama as dnama', 'details.quantity as quantity', 'details.price as price', 'details.totalamount as totalamount')
            ->where('fees.id', $feesid)
            ->orderBy('details.nama')
            ->get();

        // dd($getdetail);
        return view('pentadbir.fee.details.index', compact('getfees', 'getcat', 'getdetail'));
    }

    public function create(Request $request)
    {
        //
        // dd($request);
        $getfees = $request->id;
        $cat = DB::table('categories')->get();

        return view('pentadbir.fee.details.add', compact('cat', 'getfees'));
    }

    public function store(Request $request)
    {

        // $liststd =  DB::table('class_student')
        //     ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
        //     ->join('classes', 'classes.id', '=', 'class_organization.class_id')
        //     ->join('class_fees', 'class_fees.class_organization_id', '=', 'class_organization.id')
        //     ->select('class_student.id as cid')
        //     ->where('class_fees.fees_id', $request->id)
        //     ->get();
        // dd($liststd);

        // dd($request->id);


        $this->validate($request, [
            'name'         =>  'required',
            'price'        =>  'required',
            'quantity'     =>  'required',
            'cat'          =>  'required',
        ]);

        // price must in decimal
        
        $no = (doubleval($request->get('price')));
        $total = $no * $request->get('quantity');
        // dd($total);
        $detail = new Detail([
            'nama'         =>  $request->get('name'),
            'price'        =>  $request->get('price'),
            'quantity'     =>  $request->get('quantity'),
            'totalamount'  =>  $total,
            'category_id'  =>  $request->get('cat'),
        ]);

        $detail->save();

        $fdid = DB::table('fees_details')->insertGetId([
            'status'     => 1,
            'details_id' => $detail->id,
            'fees_id'    => $request->id
        ]);

        // insert student first
        $liststd =  DB::table('class_student')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('class_fees', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->select('class_student.id as cid')
            ->where('class_fees.fees_id', $request->id)
            ->get();

        // dd($liststd);

        for ($i = 0; $i < count($liststd); $i++) {
            $array[] = array(
                'status'            => 'Berhutang',
                'class_student_id'  => $liststd[$i]->cid,
                'fees_details_id'   => $fdid
            );
        }

        DB::table('student_fees')->insert($array);



        // sum values
        $getsum  = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->where('fees.id', $request->id)
            ->sum('details.totalamount');

        // dd($getsum);
        // update total amount

        $fees = DB::table('fees')
            ->where('id', $request->id)
            ->update(['totalamount' => $getsum]);


        return redirect('/getdetails/' . $request->id)->with('success', 'New details has been added successfully');
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
}
