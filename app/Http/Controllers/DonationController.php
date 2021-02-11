<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donate = Donation::all();
        return view('donate.index', compact('donate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('donate.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
        return redirect('/donate')->with('success', 'New donations has been added successfully');
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
        $donation = DB::table('donations')->where('id', $id)->first();

        return view('donate.update', compact('donation'));
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
