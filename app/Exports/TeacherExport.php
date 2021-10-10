<?php

namespace App\Exports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeacherExport implements FromCollection, ShouldAutoSize, WithHeadings
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
            ['organization_user.role_id', 5]
        ])
        ->orderBy('users.name')
        ->get();
        
        return $listteacher;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'No. kp',
            'email',
            'No. Tel Bimbit'
        ];
    }
}
