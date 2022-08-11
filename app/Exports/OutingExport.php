<?php

namespace App\Exports;

use App\Outing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OutingExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $userid     = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        // dd($userid);

        $listclass = DB::table('outings')
            ->select('outings.start_date_time', 'outings.end_date_time', 'outings.organization_id')
            ->where([
                ['outings.organization_id', $school->schoolid],
            ])
            ->orderBy('outings.start_date_time')
            ->get();
        // dd($listclass);
        return $listclass;
    }

    public function headings(): array
    {
        return [
            'Tarikh dan Masa Keluar',
            'Tarikh dan Masa Masuk'
        ];
    }
}
