<?php

namespace App\Exports;

use App\ClassModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return ClassModel::all();

        $userid     = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        // dd($userid);

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $school->schoolid],
                ['classes.status', 1]
            ])
            ->orderBy('classes.nama')
            ->get();

        return $listclass;
    }

    public function headings(): array
    {
        return [
            'Nama Kelas',
            'Tahap Kelas',
        ];
    }
}
