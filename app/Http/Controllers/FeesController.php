<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $fees = DB::table('fees')->orderBy('nama')->get();
        return view('pentadbir.fee.index', compact('fees'));
    }

    public function parentpay()
    {
        $fees = DB::table('fees')->orderBy('nama')->get();
        return view('parent.fee.index', compact('fees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pentadbir.fee.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            ->where('classes.nama', 'LIKE',  '%' .$yearstd . '%')
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
