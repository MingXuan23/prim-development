<?php

namespace App\Exports;

use App\Models\Dorm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DormStudentlistExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($organId, $dorm)
    {
        $this->organId = $organId;
        $this->dorm = $dorm;
    }

    public function collection()
    {


        // dd($this->organId);
        $studentlist = DB::table('class_student')
            ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->join('class_organization as co', 'co.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'co.class_id')
            ->select('students.nama as studentName', 'classes.nama as className', 'dorms.name as dormName', 'class_student.blacklist as blacklist')
            ->where([
                ['class_student.dorm_id', $this->dorm],
                ['dorms.organization_id', $this->organId],
            ])
            ->orderBy('students.nama')
            ->get();



        return $studentlist;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kelas',
            'Dorm',
            'Blacklist Status'
        ];
    }
}
