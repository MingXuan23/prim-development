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
        return view('homestay.listhomestay');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function getHomestayDatatable()
    {
        $homestayList = $this->getHomestayByUserId();
        
        if (request()->ajax()) {
            return datatables()->of($homestayList)->make(true);
        }
    }

    public static function getHomestayByUserId()
    {

            $userId = Auth::id();
            return Homestay::where('ownerid', $userId)->get();

        
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
