<?php

namespace App\Exports;

use App\Student;
use App\Dorm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResidentExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($organId, $dormId)
    {
        $this->organId = $organId;
        $this->dormId = $dormId;
    }

    public function collection()
    {
        $listresident = DB::table('class_student')
            ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->join('class_organization as co', 'co.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'co.class_id')
            ->select('students.nama as studentname', 'classes.nama as classname', 'class_student.outing_status', 
            'class_student.blacklist', 'dorms.name as dormname')
            ->where([
                ['dorms.organization_id', $this->organId],
                ['dorms.id', $this->dormId],
                ['class_student.status', 1],
            ])
            ->orderBy('students.nama')
            ->get();
        // dd($listclass);

        foreach($listresident as $listresidents){
            if($listresidents->outing_status == 0){
                $listresidents->outing_status = "Dalam";
            }
            else{
                $listresidents->outing_status = "Keluar";
            }

            if($listresidents->blacklist == 0){
                $listresidents->blacklist = "Tidak";
            }
            else{
                $listresidents->blacklist = "Ya";
            }
        }

        // dd($listresident);
        return $listresident;
    }

    public function headings(): array
    {
        return [
            'Nama Pelajar',
            'Kelas',
            'Status Keluar',
            'Blacklist',
            'Nama Asrama',
        ];
    }
}
