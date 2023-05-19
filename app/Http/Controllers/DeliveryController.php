<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DeliveryController extends Controller
{
    public function index()
    {
        $price=DB::table('parcel_delivery_price')->get();
        //dd($price);
         return view('delivery.index',compact('price'));
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();
        return view('activity.add', compact('organization'));
    }

    public function store(Request $request)
    {
        
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
