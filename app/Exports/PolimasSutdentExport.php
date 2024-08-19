<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PolimasSutdentExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    private $organId = 107;

    public function __construct($organId, $kelasId)
    {
        $this->kelasId = $kelasId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        set_time_limit(1200);
        $liststudents = DB::table('organization_user_student as ous')
        ->join('students', 'students.id', '=', 'ous.student_id')
        ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
        ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
        ->join('classes as c', 'c.id', '=', 'co.class_id')
        ->join('organization_user as ou', 'ou.id', 'ous.organization_user_id')
        ->join('users', 'users.id', 'ou.user_id')
        ->select('users.name', 'c.nama', 'users.name', 'users.icno', 'users.telno', 'cs.status','cs.id as csid')
        ->where([
            ['co.organization_id', $this->organId],
            ['c.id', $this->kelasId],
            ['cs.status', 1],
            ['ou.role_id', 6],
        ])
        ->orderBy('students.nama')
        ->get();

        foreach ($liststudents as $key => $student) {
            # code...
            $student->telno = '`' . $student->telno;
            $student->icno =  $student->icno ==null?$student->telno: '`'.$student->icno;

            $isPaid = DB::table('student_fees_new as sfn')
                ->leftJoin('fees_new as fn', 'fn.id', 'sfn.fees_id')
                ->where('sfn.status', 'Paid')
                ->where('sfn.class_student_id', $student->csid)
                ->where('fn.name','LIKE','%Yuran Konvokesyen%')
                ->select('fn.name')
                ->first();
            if ($isPaid)
            {
                if (strpos($isPaid->name, 'Tidak Hadir'))
                {
                    $student->status = "Tidak Hadir";
                }
                else
                {
                    $student->status = "Hadir";
                }
            }
            else
            {
                $student->status = "Belum Bayar";
            }

            unset($student->csid);
        }
        

        return $liststudents;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Batch',
            'No Kad Pengenalan',
            'Telno',
            'Status',
        ];
    }
}
