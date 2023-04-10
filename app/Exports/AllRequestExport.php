<?php

namespace App\Exports;

use App\Models\Dorm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllRequestExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($organId, $from, $to)
    {
        $this->organId = $organId;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        // dd($this->organId);
        $data = DB::table('students')
        ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
        ->join('student_outing as so', 'so.class_student_id', '=', 'cs.id')
        ->join('organization_user_student as ous', 'ous.student_id', '=', 'students.id')
        ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
        ->join('organization_roles as or', 'or.id', '=', 'ou.role_id')
        ->join('classifications', 'classifications.id', '=', 'so.classification_id')
        ->where([
            ['so.status', '>', 0],
            ['classifications.organization_id',  $this->organId],
        ])
        ->whereBetween('so.apply_date_time', [$this->from, $this->to])
        ->select('classifications.fake_name as catname', DB::raw('count("so.id") as total'))
        ->groupBy('classifications.fake_name')
        ->get();

        // dd($studentlist);
        return $data;
    }

    public function headings(): array
    {
        return 
        [
            ['Laporan Permintaan Keluar Pelajar pada ' . $this->from .' hingga '. $this->to],
            ['Kategori',
            'Bilangan Permintaan',],
        ];
    }
}
