<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Symfony\Component\VarDumper\Cloner\Data;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportYuranStatus implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct($yuran)
    {
        $this->yuran = $yuran;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->yuran->category == "Kategory A")
        {
            $data = DB::table('students as s')
                ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
                ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                ->leftJoin('classes as c', 'c.id', 'co.class_id')
                ->leftJoin('fees_new_organization_user as fou', 'fou.organization_user_id', 'ou.id')
                ->where('fou.fees_new_id', $this->yuran->id)
                ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'fou.status')
                ->orderBy('c.nama')
                ->orderBy('s.nama')
                ->get();
        }
        else
        {
            $data = DB::table('students as s')
                ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                ->leftJoin('classes as c', 'c.id', 'co.class_id')
                ->leftJoin('student_fees_new as sfn', 'sfn.class_student_id', 'cs.id')
                ->where('sfn.fees_id', $this->yuran->id)
                ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'sfn.status')
                ->orderBy('c.nama')
                ->orderBy('s.nama')
                ->get();
        }
        
        foreach ($data as $key => $student) {
            $student->status = $student->status == "Debt" ? "Masih Berhutang" : "Telah Bayar";
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kelas',
            'Jantina',
            'Status',
        ];
    }
}
