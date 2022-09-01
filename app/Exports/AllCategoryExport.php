<?php

namespace App\Exports;

use App\Models\Dorm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllCategoryExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($studentid, $from, $until)
    {
        $this->studentid = $studentid;
        $this->from = $from;
        $this->until = $until;
    }

    public function collection()
    {
        $data = DB::table('student_outing')
            ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
            ->join('organization_user as warden', 'warden.id', '=', 'student_outing.warden_id')
            ->join('users as wardenUser', 'wardenUser.id', '=', 'warden.user_id')
            ->join('organization_user as guard', 'guard.id', '=', 'student_outing.guard_id')
            ->join('users as guardUser', 'guardUser.id', '=', 'guard.user_id')
            ->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
            ->select(
                'classifications.name as classificationName',
                'student_outing.out_date_time as outTime',
                'student_outing.reason',
                'wardenUser.name as wardenName',
                'student_outing.in_date_time as inTime',
                'guardUser.name as guardName'
            )
            ->orderBy('student_outing.apply_date_time');
        if ($this->from == null || $this->until == null) {
            $data = $data
                ->where('class_student.id', $this->studentid)
                ->get();
        } else {
            $data = $data
                ->where('class_student.id', $this->studentid)
                ->where('student_outing.apply_date_time', '>=', $this->from)
                ->where('student_outing.apply_date_time', '<=', $this->until)
                ->get();
        }


        return $data;
    }

    public function headings(): array
    {
        return [
            'Kategori Balik',
            'Tarikh Masa Keluar',
            'Sebab',
            'Warden Bertanggungjawab',
            'Tarikh Masa Masuk',
            'Guard Bertanggungjawab'
        ];
    }
}
