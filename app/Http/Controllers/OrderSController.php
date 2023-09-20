<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Jajahan\Jajahan;
use App\Models\OrganizationHours;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\TypeOrganization;
use App\Models\OrganizationRole;
use App\Models\Dish_Available;
use App\Models\Order;
use App\Models\Order_Dish;
use App\Models\Dish;
use App\Models\Dish_Type;
use View;
use DateTime;
use DateInterval;
use DatePeriod;
use Hash;
use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;


class OrderSController extends Controller
{   
    public function managemenu(){
        $userId = Auth::id();
        $data = DB::table('organizations as o')
        ->leftJoin('organization_user as ou', 'o.id', '=', 'ou.organization_id')
        ->select("o.*")
        ->distinct()
        ->where('o.type_org', '12')
        ->where('ou.user_id',$userId)
        ->get();

        return view('orders.menu', compact('data'));
    }

    public function listmenu($organizationId){
        //dd($organizationId);
        $userId = Auth::id();
        $data = Dish::join('dish_type as dt', 'dt.id', '=', 'dishes.dish_type')
        ->where('organ_id',$organizationId)
        ->select('dishes.id', 'dishes.name as dishname', 'dishes.price', 'dt.name as dishtype')
        ->get();

        $org_name = Organization::where('id',$organizationId)
        ->select('nama')
        ->get();
        //dd($org_name);

        // foreach ($org_name as $record){
        //     $nama = $record->nama;
        // }

        $nama = $org_name->isEmpty() ? '' : $org_name[0]->nama;
        $dishtype = DB::table('dish_type')->get();

        return view('orders.listmenu', compact('data','nama','organizationId','dishtype'));
    }

    public function addmenu($organizationId){
        //dd($organizationId);
        $data = DB::table('dish_type')->get();
        return view('orders.addmenu', compact('data','organizationId'));
    }

    public function processaddmenu(Request $request, $organizationId){
        $request->validate([
            'dishname' => 'required',
            'dishtype' => 'required',
            'price' => ['required', 'regex:/^\d{1,6}(\.\d{1,2})?$/'] // Matches double(8,2)
        ]);
    
        $dish = new Dish();
        $dish->name = $request->dishname;
        $dish->price = $request->price;
        $dish->organ_id = $organizationId;
        $dish->dish_type = $request->dishtype;
        $result = $dish->save();
    
        if($result)
        {
            return back()->with('success', 'Menu Berjaya Ditambah');
        }
        else
        {
            return back()->withInput()->with('error', 'Menu Gagal Ditambah');
        }
    }

    public function editmenu(Request $request){
        //dd($request->all());

        $request->validate([
            'dishname' => 'required',
            'dishtype' => 'required',
            'price' => ['required', 'regex:/^\d{1,6}(\.\d{1,2})?$/'] // Matches double(8,2)
        ]);
        
        $updatedRows = DB::table('dishes')
            ->where('id', $request->dishid)
            ->update([
                'name' => $request->dishname,
                'price' => $request->price,
                'dish_type' => $request->dishtype
            ]);
        
        if ($updatedRows) {
            return back()->with('success', 'Menu Berjaya Disunting');
        } else {
            return back()->withInput()->with('error', 'Menu Gagal Disunting');
        }        
    }
}
