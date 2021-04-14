<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{

    public function index()
    {
        $organization = $this->getOrganizationByUserId();
        return view('activity.index', compact('organization'));
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        $listorg = Organization::whereHas('user', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return $listorg;
    }

    public function getActivityDatatable(Request $request)
    {
        // $oid = 0;
        
        $listactivity = Organization::find(1)->activity;
        // $listactivity = DB::table('organizations')
        // ->join('activities', 'activities.organization_id', '=', 'organizations.id')
        // ->select('activities.name as aname')
        // ->where('organizations.id', 1)
        // ->orderBy('activities.name')
        // ->get();
        // dd($listactivity);

        if (request()->ajax()) {

            return datatables()->of($listactivity)
            ->addColumn('status', function ($data) {
                if ($data->status == '1') {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-success">Aktif</span></div>';
                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Tidak Aktif </span></div>';
                    return $btn;
                }
            })
            ->addColumn('action', function ($data) {

                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('activity.edit', $data->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $data->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
            
        }

        // dd($listactivity);

        // if (request()->ajax()) {
        //     return datatables()->of($listdonor)
        //         ->editColumn('amount', function ($data) {
        //             return number_format($data->amount, 2);
        //         })
        //         ->make(true);
        // }
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
            'organization_id' =>  2,
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
