<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use View;
use App\Models\Homestay;

class HomestayController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $data = Homestay::where('ownerid', $userId)->get();

        return view('homestay.listhomestay', compact('data'));
    }

    public function createhomestay()
    {
        return view('homestay.createhomestay');
    }

    public function inserthomestay(Request $request)
    {
        
        $userId = Auth::id();
        $request->validate([
            'name' => 'required|unique:homestays,name',
            'location' =>'required',
            'pno' =>'required|numeric',
            'stat' =>'required'
            ]);

            $homestay = new Homestay();
            $homestay->name = $request->name;
            $homestay->location = $request->location;
            $homestay->pno = $request->pno;
            $homestay->status = $request->stat;
            $homestay->ownerid = $userId;
            $result = $homestay->save();

            if($result)
        {
            return back()->with('success', 'Homestay Berjaya Ditambah');
        }
        else
        {
            return back()->withInput()->with('error', 'Homestay Telahpun Didaftarkan');

        }
    }

    public function store(Request $request)
    {
        //
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
