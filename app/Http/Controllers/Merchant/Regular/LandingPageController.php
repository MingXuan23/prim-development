<?php

namespace App\Http\Controllers\Merchant\Regular;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Merchant\RegularMerchantController;
use App\Models\Organization;
use App\Models\OrganizationHours;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function index()
    {
        $all_merchant_org = null;
        $merchants = null;
        $is_open = array();
        $opened_org = array();
        
        $type_org_id = DB::table('type_organizations')->where('nama', 'Peniaga Barang Umum')->first()->id;

        $all_merchant_org = Organization::
        where([
            ['deleted_at', null],
            ['type_org', $type_org_id],
        ])
        ->select('id', 'nama', 'address', 'postcode', 'state', 'city', 'organization_picture')
        ->get();
        
        foreach($all_merchant_org as $row)
        {
            $is_open[$row->id] = $this->isOrgOpenThisWeek($row->id);
            if($is_open[$row->id] == true) {
                $opened_org[] = $row->id;
            }
        }

        $merchants = Organization::whereIn('id', $opened_org)->select('id', 'nama', 'address', 'postcode', 'state', 'city', 'organization_picture')->get();

        return view('merchant.regular.index', compact('merchants', 'is_open'));
    }

    public function test_index(Request $request)
    {
        $merchant_arr = array();
        if($request->ajax()) {
            $todayDate = Carbon::now()->format('l'); // Format to day name
        
            $day = RegularMerchantController::getDayIntegerByDayName($todayDate); // Convert to integer
            $type_org_id = DB::table('type_organizations')->where('nama', 'Peniaga Barang Umum')->first()->id;

            $merchant = Organization::
            join('organization_hours as oh', 'oh.organization_id', 'organizations.id')
            ->where([
                ['deleted_at', null],
                ['type_org', $type_org_id],
                ['day', $day]
            ])
            ->select('organizations.id as id', 'nama', 'address', 'postcode', 'state', 'city', 'organization_picture as picture',
            'day', 'open_hour', 'close_hour', 'status')
            ->orderBy('status', 'desc')
            ->get();

            $count = 0;

            foreach($merchant as $row)
            {
                $nama = $row->nama;
                $picture = "images/koperasi/default-item.png";
                if($row->picture != null){
                    $picture = "organization_picture/".$row->picture;
                }
                if($row->status == 0) {
                    $nama = $row->nama." <label class='text-danger'>Closed</label>";
                }
                $merchant_arr[] = array(
                    "id" => $row->id,
                    "nama" => $nama,
                    "address" => $row->address,
                    "postcode" => $row->postcode,
                    "state" => $row->state,
                    "city" => $row->city,
                    "picture" => $picture,
                    "day" => $row->day,
                    "open_hour" => $row->open_hour,
                    "close_hour" => $row->status,
                );

                $count++;
            }

            return response()->json(['merchant' => $merchant_arr, 'count' => $count]);
        }
    }

    private function isOrgOpenThisWeek($org_id)
    {
        $is_open = false;

        $hours = OrganizationHours::where('organization_id', $org_id)->get();
        foreach($hours as $row) {
            if($row->status == 1) {
                $is_open = true;
            }
        }

        return $is_open;
    }
}
