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
use App\Models\OrganizationHours;
use App\Models\Donation;
use App\Models\OrganizationRole;
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

    public function fetchAvailableParentKoop(Request $request)
    {
        $state = $request->get('negeri');
        $allSchool = Organization::whereIn('type_org', [1, 2, 3])->get();
        $allKoop = Organization::where('type_org', 1039)->get();

        $isNotParent = array();
        foreach($allSchool as $school)
        {
            if(count($allKoop) != 0)
            {
                foreach($allKoop as $koop)
                {
                    if($school->id != $koop->parent_org)
                    {
                        $isNotParent[] += (int)$school->id;
                    }
                }
            }
            else
            {
                $isNotParent[] += (int)$school->id;
            }
        }

        $parent_org = Organization::whereIn('id', $isNotParent)
                    ->where('state', $state)
                    ->select('id', 'nama')
                    ->get();

        return response()->json(['success' => $parent_org]);
    }

    public function store(OrganizationRequest $request)
    {
        $link = explode(" ", $request->nama);
        $str = implode("-", $link);
        // dd($request->organization_picture);
        
        $file_name = '';

        if($this->orgIsExist($request->type_org, $request->nama)) {
            return back()->withInput()->with('error', 'Nama Organisasi Telah Diambil');
        }

        if (!is_null($request->organization_picture)) {
            $extension = $request->organization_picture->extension();
            $storagePath  = $request->organization_picture->storeAs('/public/organization-picture', $str . '.' . $extension);
            $file_name = basename($storagePath);
        }
        else
        {
            $file_name = null;
        }

        $organization = Organization::create($request->validated() + [
            'organization_picture' => $file_name,
        ]);

        $type_org = TypeOrganization::find($request->type_org);

        Organization::where('id', $organization->id)->update(['code' => $this->generateOrganizationCode($type_org->nama , $organization->id)]);
        
        //attach foreign key to pivot table
        $organization->user()->attach(Auth::id(), ['role_id' => 2]);

        $user = Auth::user();
        $user->assignRole('Admin');
        
        $role = $this->assignRoleForOrganization($type_org->nama);

        if ($request->type_org == 1 || $request->type_org == 2 || $request->type_org == 3) {
            $organization->user()->attach(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => 4]);
            $user->assignRole('Pentadbir');
        }

        // Koperasi
        if ($type_org->nama == "Koperasi") {
            Organization::where('id', $organization->id)->update(['parent_org' => $request->parent_org]);
            $organization->user()->updateExistingPivot(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => $role->id]);
            //$organization->user()->attach(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => 1239]);
            $user->assignRole($role->nama);

            $this->insertOrganizationHours($organization->id);
        }

        // Schedule Merchant
        if ($type_org->nama == "Peniaga Barang Berjadual") {
            $organization->user()->updateExistingPivot(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => $role->id]);
            $user->assignRole($role->nama);
            
            $this->insertOrganizationHours($organization->id);
        }

        // Schedule Merchant
        if ($type_org->nama == "Peniaga Barang Umum") {
            $organization->user()->updateExistingPivot(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => $role->id]);
            $user->assignRole($role->nama);

            $this->insertOrganizationHours($organization->id);
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
                        ->whereIn('role_id', [2, 1239])
                        ->get();
            
            if($exists->isEmpty()){
                return view('errors.404');
            }
        }

        $type_org = TypeOrganization::all();

        $org = DB::table('organizations')->where('id', $id)->first();

        $states = Jajahan::negeri();

        // Koperasi
        if($org->type_org == 1039)
        {
            $parent_org = $this->getAvailableSchoolForKoop();

            $org_parent_name = Organization::where('id', $org->parent_org)->first();

            return view('organization.update', compact('org', 'type_org', 'parent_org', 'org_parent_name', 'states'));
        }

        return view('organization.update', compact('org', 'type_org', 'states'));
    }

    public function update(OrganizationRequest $request, $id)
    {
        Organization::where('id', $id)->update($request->validated());

        if (isset($request->seller_id)) {
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
            ->whereIn('ou.role_id', [2, 1239])
            ->get();
        }
        
    }

    public function getAllOrganization()
    {
        return view('organization.index');
    }

    public function generateOrganizationCode($typeOrg, $id)
    {
        if ($typeOrg == "SK /SJK") {
            $code = 'SK' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "SRA /SRAI") {
            $code = 'SA' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "SMK /SMJK") {
            $code = 'SM' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "Masjid") {
            $code = 'MS' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "NGO") {
            $code = 'NGO' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "Rumah Anak Yatim") {
            $code = 'RAY' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "Pusat Tahfiz") {
            $code = 'PT' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "Koperasi") { // Koperasi
            $code = 'KP' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "Peniaga Barang Berjadual") { // Schedule Merchant
            $code = 'PBJ' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == "Peniaga Barang Umum") { // Regular Merchant
            $code = 'PBU' . str_pad($id, 5, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function testRepeater()
    {
        $states = Jajahan::negeri();
        return view('test.repeater', compact('states'));
    }

    private function assignRoleForOrganization($type_org_name)
    {
        $role = '';

        if($type_org_name == "Koperasi")
        {
            $role = OrganizationRole::where('nama', '=', 'Koop Admin')->first();
        } 
        else if($type_org_name == "Peniaga Barang Berjadual")
        {
            $role = OrganizationRole::where('nama', '=', 'Schedule Merchant Admin')->first();
        }
        else if($type_org_name == "Peniaga Barang Umum")
        {
            $role = OrganizationRole::where('nama', '=', 'Regular Merchant Admin')->first();
        }

        return $role;
    }

    public function insertOrganizationHours($id)
    {
        OrganizationHours::insert([
            [
                'day' => 1,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'day' => 2,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 3,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 4,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 5,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 6,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 0,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function getAvailableSchoolForKoop()
    {
        $allSchool = Organization::whereIn('type_org', [1, 2, 3])->get();
        $allKoop = Organization::where('type_org', 1039)->get();

        $isNotParent = array();
        foreach($allSchool as $school)
        {
            if(count($allKoop) != 0)
            {
                foreach($allKoop as $koop)
                {
                    if($school->id != $koop->parent_org)
                    {
                        $isNotParent[] += (int)$school->id;
                    }
                }
            }
            else
            {
                $isNotParent[] += (int)$school->id;
            }
        }
        
        $parent_org = Organization::whereIn('id', $isNotParent)->get();

        return $parent_org;
    }

    private function orgIsExist($type_org_id, $org_name)
    {
        $type_org = TypeOrganization::find($type_org_id);
        $org = Organization::where('type_org', $type_org->id)->select('nama')->get();

        if($type_org->nama == 'Peniaga Barang Umum') {
            foreach($org as $row) {
                if($row->nama == $org_name) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}