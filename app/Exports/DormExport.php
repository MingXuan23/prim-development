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
            ->select('dorms.name', 'dorms.accommodate_no', 'dorms.student_inside_no')
            ->where([
                //['organization.id', $this->organId],
                ['dorms.organization_id', $this->organId],
            ])
            ->orderBy('dorms.name')
            ->get();

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
