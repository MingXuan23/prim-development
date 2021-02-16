<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function index()
    {
        $donate = Donation::all();
        return view('donate.index', compact('donate'));
    }

    public function indexDerma(Request $request)
    {

        if (request()->ajax()) {
            $oid = $request->oid;

            if ($oid) {
                // $data = Donation::whereHas('organization', function ($query) use ($oid) {
                //     $query->where('organization_id', $oid);
                // })->get();
                $data = DB::table('organizations')
                    ->join('donation_organization', 'donation_organization.organization_id', '=', 'organizations.id')
                    ->join('donations', 'donations.id', '=', 'donation_organization.donation_id')
                    ->select('donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status')
                    ->where('organizations.id', $oid)
                    ->orderBy('donations.nama');
            } else {
                $data = DB::table('donations')
                    ->select('donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status')
                    ->orderBy('donations.nama');;
            }
            // dd($data);

            return datatables()->of($data)
                ->addColumn('status', function ($row) {
                    // dd($row);
                    if ($row->status == '1') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<button class="btn btn-success m-1"> Aktif </button></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<button  class="btn btn-danger m-1"> Aktif </button></div>';

                        return $btn;
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('donate.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $organization = Organization::get();

        return view('paydonate.index', compact('organization'));
    }

    public function create()
    {
        $organization = Organization::get();

        return view('donate.add', compact('organization'));
    }

    public function store(Request $request)
    {
        // dd($request->get('organization'));

        $this->validate($request, [
            'organization'  =>  'required|not_in:0',
            'name'          =>  'required',
            'description'   =>  'required',
            'start_date'    =>  'required',
            'end_date'      =>  'required'
        ]);

        $dt = Carbon::now();
        $startdate  = $dt->toDateString($request->get('start_date'));
        $enddate    = $dt->toDateString($request->get('end_date'));

        $newdonate = new Donation([
            'nama'           =>  $request->get('name'),
            'description'    =>  $request->get('description'),
            'date_created'   =>  now(),
            'date_started'   =>  $startdate,
            'date_end'       =>  $enddate,
            'status'         =>  '1',
        ]);
        $newdonate->save();

        $newdonate->organization()->attach($request->get('organization'));

        return redirect('/donate')->with('success', 'New donations has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $donation = DB::table('donations')->where('id', $id)->first();

        return view('donate.update', compact('donation'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'          =>  'required',
            'description'   =>  'required',
            'price'         =>  'required|numeric'
        ]);

        DB::table('donations')
            ->where('id', $id)
            ->update(
                [
                    'nama'           =>  $request->get('name'),
                    'description'    =>  $request->get('description'),
                    'amount'         =>  $request->get('price'),
                ]
            );

        return redirect('/donate')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        $result = Donation::find($id)->delete();

        if ($result) {
            Session::flash('success', 'Donation Delete Successfully');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Donation Delete Failed');
            return View::make('layouts/flash-messages');
        }
    }
}
