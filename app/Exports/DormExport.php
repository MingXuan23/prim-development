<?php

namespace App\Exports;

use App\Models\Dorm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DormExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($organId)
    {
        $this->organId = $organId;
    }

    public function collection()
    {

        // dd($this->organId);
        $listDorms = DB::table('dorms')
            ->join('class_organization', 'class_organization.dorm_id', '=', 'dorms.id')
            //->join('organization', 'organization.id', '=', 'class_organization.organization_id')
            ->select('dorms.name', 'dorms.accommodate_no', 'dorms.student_inside_no')
            ->where([
                //['organization.id', $this->organId],
                ['class_organization.organization_id', $this->organId],
            ])
            ->orderBy('dorms.name')
            ->get();

        // $listteachers = DB::table('users')
        // ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
        // ->select('users.name', 'users.icno', 'users.email', 'users.telno')
        // ->where([
        //     ['organization_user.organization_id', $this->organId],
        //     ['organization_user.role_id', 5]
        // ])
        // ->orderBy('users.name')
        // ->get();

        return $listDorms;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kapasiti',
            'Bilangan pelajar dalam'
        ];
    }
}
