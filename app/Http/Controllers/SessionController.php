<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
        // dd($request);
        // $request->session()->put('my_name','Virat Gandhi');
    }

    public function accessSessionData(Request $request)
    {

        // dd($request);

        if ($request->session()->has('id'))
            echo $request->session()->get('id');
        // echo 'data ade';

        else
            echo 'No data in the session';
    }

    public function storeSessionData(Request $request)
    {

        $size = count(collect($request)->get('id'));

        $id = collect($request)->get('id');

        $request->session()->put('id', $id);
        echo "Data has been added to session";
    }

    public function deleteSessionData(Request $request)
    {
        $request->session()->forget('id');
        echo "Data has been removed from session.";
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
