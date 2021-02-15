<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function index()
    {
        $donate = Donation::all();
        return view('donate.index', compact('donate'));
    }

    public function indexDerma()
    {
        $organization = Organization::get();
        $donate = Donation::all();

        // dd($organization);
        return view('paydonate.index', compact('organization', 'donate'));
    }

    public function fetchDonation(Request $request)
    {
        // dd($request->get('oid'));

        $organization = Organization::get();
        $oid = $request->get('oid');
        
        $donate = Donation::whereHas('organization', function ($query) use ($oid) {
            $query->where('organization_id', $oid);
        })->get();

        // dd($donate);

        return response()->json(['success' => $donate]);

        // return view('paydonate.index', compact('organization', 'donate'));
    }

    public function create()
    {
        return view('donate.add');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'          =>  'required',
            'description'   =>  'required',
            'price'         =>  'required|numeric'
        ]);

        $newdonate = new Donation([
            'nama'           =>  $request->get('name'),
            'description'    =>  $request->get('description'),
            'amount'         =>  $request->get('price'),
            'date_created'   =>  now(),
            'status'         =>  '1',
        ]);
        $newdonate->save();

        $newdonate->organization()->attach(1);

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
        //
    }
}
