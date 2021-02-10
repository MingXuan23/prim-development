<?php

namespace App\Exports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class TeacherExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return Teacher::all(['name', 'icno', 'email', 'telno'])->where;

        $userid     = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        $listteacher = DB::table('users')
        ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
        ->select('users.name', 'users.icno', 'users.email', 'users.telno')
        ->where([
            ['organization_user.organization_id', $school->schoolid],
            ['organization_user.role_id', 2]
        ])
        ->orderBy('users.name')
        ->get();
        
        return $listteacher;
    }
}
