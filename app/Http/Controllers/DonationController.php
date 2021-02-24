<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DonationController extends Controller
{
    public function index()
    {
        $organization = $this->listOrganizationByUserId();
        return view('donate.index', compact('organization'));
    }

    public function indexDerma()
    {
        $organization = $this->listAllOrganization();
        return view('paydonate.index', compact('organization'));
    }

    public function getDonationByOrganizationDatatable(Request $request)
    {


        if (request()->ajax()) {

            $oid = $request->oid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {
                $data = DB::table('organizations')
                    ->join('donation_organization', 'donation_organization.organization_id', '=', 'organizations.id')
                    ->join('donations', 'donations.id', '=', 'donation_organization.donation_id')
                    ->select('organizations.id as oid', 'donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status')
                    ->where('organizations.id', $oid)
                    ->orderBy('donations.nama');
            } elseif ($hasOrganizaton == "false") {
                $data = DB::table('donations')
                    ->join('donation_organization', 'donation_organization.donation_id', '=', 'donations.id')
                    ->join('organizations', 'organizations.id', '=', 'donation_organization.organization_id')
                    ->select('organizations.id as oid', 'donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status')
                    ->orderBy('donations.nama');
            } elseif ($hasOrganizaton == "true") {
                $data = DB::table('organizations')
                    ->join('donation_organization', 'donation_organization.organization_id', '=', 'organizations.id')
                    ->join('donations', 'donations.id', '=', 'donation_organization.donation_id')
                    ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
                    ->join('users', 'users.id', '=', 'organization_user.user_id')
                    ->select('organizations.id as oid', 'donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status')
                    ->where('users.id', $userId)
                    ->orderBy('donations.nama');
            }

            // dd($data->oid);
            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if ($row->status == '1') {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<button class="btn btn-success m-1"> Aktif </button></div>';

                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<button  class="btn btn-danger m-1"> Tidak Aktif </button></div>';

                    return $btn;
                }
            });

            if ($hasOrganizaton == "false") {

                $table->addColumn('action', function ($row) {
                    $token = csrf_field();
                    $oid = $row->oid;
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<form method="POST" action="' . route('fpxIndex') . '"
                            enctype="multipart/form-data">
                            ' . $token . '
                            <input type="hidden" name="amount" id="amount" value="6">
                            <input type="hidden" name="o_id" id="o_id" value=" ' . $row->oid . ' ">
                            <input type="hidden" name="donate_id" id="donate_id" value=" ' . $row->id . ' ">
                            <button class="btn btn-primary float-right" type="submit" onclick="">Bayar Sekarang</button>
                            </form></div>';
                    return $btn;
                });
            } else {
                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('donate.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                });
            }
            $table->rawColumns(['status', 'action']);
            return $table->make(true);
        }
    }

    public function listAllOrganization()
    {
        $organization = Organization::get();
        return $organization;
    }

    public function listOrganizationByUserId()
    {
        $userId = Auth::id();

        $listorg = Organization::whereHas('user', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return $listorg;
    }

    public function create()
    {
        $organization = $this->listOrganizationByUserId();

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
