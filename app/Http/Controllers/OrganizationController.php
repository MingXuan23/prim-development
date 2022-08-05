<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use App\Models\TypeOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Jajahan\Jajahan;
use App\Models\Donation;
use View;

class OrganizationController extends Controller
{
    public function index()
    {
        return view('organization.index');
    }

    public function create()
    {
        // after launch remove where
        $type_org = TypeOrganization::all();

        $states = Jajahan::negeri();
        return view('organization.add', compact('type_org', 'states'));
    }

    public function getDistrict(Request $request)
    {
        $districts = Jajahan::daerah($request->state_id);
        return $districts;
    }

    public function store(OrganizationRequest $request)
    {
        $link = explode(" ", $request->nama);
        $str = implode("-", $link);
        // dd($request->organization_picture);
        $extension = $request->organization_picture->extension();
        $file_name = '';

        if (!is_null($request->organization_picture)) {
            $storagePath  = $request->organization_picture->storeAs('/public/organization-picture', $str . '.' . $extension);
            $file_name = basename($storagePath);
        }

        $organization = Organization::create($request->validated() + [
            'organization_picture' => $file_name,
        ]);

        Organization::where('id', $organization->id)->update(['code' => $this->generateOrganizationCode($request->type_org, $organization->id)]);

        //attach foreign key to pivot table
        $organization->user()->attach(Auth::id(), ['role_id' => 2]);

        $user = Auth::user();
        $user->assignRole('Admin');

        if ($request->type_org == 1 || $request->type_org == 2 || $request->type_org == 3) {
            $organization->user()->attach(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => 4]);
            $user->assignRole('Pentadbir');
        }

        return redirect('/organization')->with('success', 'Organisasi Berjaya Ditambah');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // after launch remove where
        // $type_org = TypeOrganization::where('id', 4)->orWhere('id', 5)->get();
        // $type_org = TypeOrganization::whereNotIn('id', array(1, 2, 3))->get();
        
        // check if organizations doesnt belong to the user
        if (!Auth::user()->hasRole('Superadmin')) {
            $user = Auth::user();
            $exists = DB::table('organization_user')
                        ->where('user_id', $user->id)
                        ->where('organization_id', $id)
                        ->where('role_id', 2)
                        ->get();
            
            if($exists->isEmpty()){
                return view('errors.404');
            }
        }

        $type_org = TypeOrganization::all();

        $org = DB::table('organizations')->where('id', $id)->first();

        $states = Jajahan::negeri();

        return view('organization.update', compact('org', 'type_org', 'states'));
    }

    public function update(OrganizationRequest $request, $id)
    {
        Organization::where('id', $id)->update($request->validated());

        if(isset($request->seller_id))
        {
            Organization::where('id', $id)->update([
                'seller_id'         => $request->seller_id,
                'fixed_charges'      =>  $request->fixed_charges,
            ]);
        }

        return redirect('/organization')->with('success', 'Maklumat Organisasi Berjaya Dikemaskini');
    }

    public function destroy($id)
    {
        $result = Organization::find($id)->delete();

        if ($result) {
            Session::flash('success', 'Organisasi Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Organisasi Tidak Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function getOrganizationDatatable()
    {
        $organizationList = $this->getOrganizationByUserId();

        if (request()->ajax()) {
            return datatables()->of($organizationList)
                ->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('organization.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                })
                ->make(true);
        }
    }

    public static function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {
            return Organization::all();
        } else {
        $userId = Auth::id();
            return DB::table('organizations as o')
            ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
            ->select("o.*")
            ->distinct()
            ->where('ou.user_id', $userId)
            ->whereIn('ou.role_id', [2, 4])
            ->get();
        }
    }

    public function getAllOrganization()
    {
        return view('organization.index');
    }

    public function generateOrganizationCode($typeOrg, $id)
    {
        if ($typeOrg == 1) {
            $code = 'SK' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == 2) {
            $code = 'SA' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == 3) {
            $code = 'SM' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == 4) {
            $code = 'MS' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == 5) {
            $code = 'NGO' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == 6) {
            $code = 'RAY' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == 7) {
            $code = 'PT' . str_pad($id, 5, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function testRepeater()
    {
        $states = Jajahan::negeri();
        return view('test.repeater', compact('states'));
    }

    

     // to get all organization with type_org = 8
     public function getAllOrgTypeFood()
     {
         return DB::table('organizations')
             ->where('type_org', 8)
             ->get();
     }
}
