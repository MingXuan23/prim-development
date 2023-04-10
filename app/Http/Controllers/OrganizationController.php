<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use App\Models\TypeOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use App\Http\Jajahan\Jajahan;
use App\Models\OrganizationHours;
use App\Models\Donation;
use Illuminate\Support\Facades\Validator;
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
        $parent_org = Organization::whereIn('type_org', [1, 2, 3])->get();

        return response()->json(['success' => $parent_org]);
    }

    public function store(OrganizationRequest $request)
    {
        if ($request->type_org == 10)
        {
            Validator::make($request->all(), [
                'parent_org' => "required"
            ]);
        }

        $link = explode(" ", $request->nama);
        $str = implode("-", $link);
        //dd($request);
        
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
            'parent_org'           => $request->parent_org
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
            // $organization->user()->updateExistingPivot(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => $role->id]);
            $organization->user()->attach(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => $role->id]);
            $user->assignRole($role->nama);

            $this->insertOrganizationHours($organization->id);

            // connect parent to shcool service
            $parents = DB::table('organization_user')
                ->where('organization_id', $organization->parent_org)
                ->where('role_id', 6)
                ->where('status', 1)
                ->get();

            foreach($parents as $parent)
            {
                DB::table('organization_user')
                    ->insert([
                        'organization_id' => $organization->id,
                        'user_id'         => $parent->user_id,
                        'role_id'         => $parent->role_id,
                        'status'          => 1
                    ]);
            }
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
        $type_org_id = TypeOrganization::where('nama', 'Peniaga Barang Umum')->first()->id;
        
        if(Organization::find($id)->type_org == $type_org_id){
            $this->destroyAllImages($id);
        }

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
                    $type = TypeOrganization::find($row->type_org);
                    if($type->nama != 'Peniaga Barang Umum') {
                        $token = csrf_token();
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<a href="' . route('organization.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                        $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    } else {
                        $token = csrf_token();
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<a href="' . route('admin-reg.edit-merchant', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                        $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    }
                    return $btn;
                })
                ->make(true);
        }
    }

    public static function getOrganizationByUserId()
    {
        $role_id = [];
        $userId = Auth::id();
        $roles = DB::table('organization_roles')->whereIn('nama', ['Admin', 'Regular Merchant Admin'])->get();

        foreach($roles as $row) { $role_id[] = $row->id; }

        if (Auth::user()->hasRole('Superadmin')) {
            return Organization::all();
        } else {
            $userId = Auth::id();
            return DB::table('organizations as o')
            ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
            ->select("o.*")
            ->distinct()
            ->where('ou.user_id', $userId)
            ->whereIn('ou.role_id', $role_id)
            ->where('o.deleted_at', null)
            ->get();
        }
        
    }

    public function getAllOrganization()
    {
        return view('organization.index');
    }

    public function generateOrganizationCode($typeOrg, $id) //have changed from if else to switch
    {
        $prefix="";
        switch($typeOrg)
        {
            case "SK /SJK":
                $prefix='SK';
                break;
            case "SRA /SRAI":
                $prefix='SA';
                break;
            case "SK /SJK":
                $prefix='SK';
                break;
            case "SRA /SRAI":
                $prefix='SA';
                break;
            case"SMK /SMJK":
                $prefix='SM';
                break;
            case "Masjid":
                $prefix='MS';
                break;
             case "NGO":
                $prefix='NGO';
                break;
            case "Rumah Anak Yatim":
                $prefix='RAY';
                break;
            case "Pusat Tahfiz":
                $prefix='PT';
                break;
            case "Koperasi":
                $prefix='KP';
                break;
            case"Peniaga Barang Berjadual":
                $prefix='PBJ';
                break;
            case "Peniaga Barang Umum":
                $prefix='PBU';
                break;                 
            case "PIBG Sekolah":
                $prefix='PIBG';
                break;
        }
       $code=$prefix.str_pad($id, 5, '0', STR_PAD_LEFT);
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

    private function destroyAllImages($org_id)
    {
        $org = Organization::find($org_id);

        // get existing image
        $file = public_path('/organization-picture/'.$org->organization_picture);
        
        // if the existing image is exist then delete
        if(File::exists($file))
        {
            File::delete($file);
        }

        $groups = DB::table('product_group')->where('organization_id', $org_id)->get();

        foreach($groups as $group)
        {
            $item = DB::table('product_item')->where('product_group_id', $group->id)->select('image')->get();
            foreach($item as $row)
            {
                if($row->image != NULL)
                {
                    $file = public_path("merchant-image/product-item/".$org->code."/".$row->image);
                    $exists = File::exists($file);
                    
                    if($exists)
                    {
                        File::delete($file);
                    }
                }
            }
        }
        
    }
}