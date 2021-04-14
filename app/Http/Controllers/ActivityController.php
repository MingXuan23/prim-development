<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    public function index()
    {
        //
        return view('activity.index');
    }

    public function create()
    {
        return view('activity.add');
    }

    public function store(Request $request)
    {
        //
        $dt = Carbon::now();
        $startdate  = $dt->toDateString($request->get('start_date'));
        $enddate    = $dt->toDateString($request->get('end_date'));

        $newactivity = Activity::create([
            'name'           =>  $request->get('name'),
            'description'    =>  $request->get('description'),
            'date_created'   =>  now(),
            'date_start'     =>  $startdate,
            'date_end'       =>  $enddate,
            'status'         =>  '1',
            'organization_id'=>  2,
        ]);

        return redirect('/activity')->with('success', 'New activity has been added successfully');
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
