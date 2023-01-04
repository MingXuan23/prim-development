<?php

namespace App\Http\Controllers\Merchant;

use App\Models\TypeOrganization;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegularMerchantController extends Controller
{
    public static function getOrganizationId()
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

    public static function compareDateWithToday($date)
    {
        $today = Carbon::now();
        $date_f = Carbon::parse($date);
        
        if($today->format('d-m-Y') == $date_f->format('d-m-Y')) {
            return true;
        } else {
            return false;
        }
    }

    public static function getDayIntegerByDayName($date)
    {
        $day = null;
        if($date == "Monday") { $day = 1; }
        else if($date == "Tuesday") { $day = 2; }
        else if($date == "Wednesday") { $day = 3; }
        else if($date == "Thursday") { $day = 4; }
        else if($date == "Friday") { $day = 5; }
        else if($date == "Saturday") { $day = 6; }
        else if($date == "Sunday") { $day = 0; }
        return $day;
    }
}
