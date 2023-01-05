<?php

namespace App\Http\Controllers\Merchant\AdminRegular;

use App\Models\OrganizationHours;
use App\Models\TypeOrganization;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperationHourController extends Controller
{
    private function getOrganizationId()
    {
        $role_id = DB::table('organization_roles')->where('nama', 'Regular Merchant Admin')->first()->id;
        $type_org_id = TypeOrganization::where('nama', 'Peniaga Barang Umum')->first()->id;

        $org_id = DB::table('organizations as o')
        ->join('organization_user as ou', 'ou.organization_id', 'o.id')
        ->where([
            ['user_id', Auth::id()],
            ['role_id', $role_id],
            ['status', 1],
            ['type_org', $type_org_id],
            ['deleted_at', NULL],
        ])
        ->select('o.id')
        ->first()->id;
        
        return $org_id;
    }

    public function index()
    {
        $day_name = array('Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu');
        $org_id = $this->getOrganizationId();
        $hour = OrganizationHours::where('organization_id', $org_id)->get();

        return view('merchant.regular.admin.operation-hour.index', compact('day_name', 'hour'));
    }

    public function edit(Request $request)
    {
        $day_name = array('Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu');
        $hour = OrganizationHours::find($request->hour_id);

        return response()->json(['hour' => $hour, 'day_name' => $day_name]);
    }
}
