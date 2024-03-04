<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentExport implements FromCollection, ShouldAutoSize, WithHeadings,WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */

    // protected $organId, $kelasId;

    public function __construct($organId, $kelasId)
    {
        $this->organId = $organId;
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $liststudents = DB::table('organization_user_student as ous')
        ->join('students', 'students.id', '=', 'ous.student_id')
        ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
        ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
        ->join('classes as c', 'c.id', '=', 'co.class_id')
        ->join('organization_user as ou', 'ou.id', 'ous.organization_user_id')
        ->join('users', 'users.id', 'ou.user_id')
        ->select('students.nama', 'students.gender',  'users.name', 'users.telno')
        ->where([
            ['co.organization_id', $this->organId],
            ['c.id', $this->kelasId],
            ['cs.status', 1],
            ['ou.role_id', 6],
        ])
        ->orderBy('students.nama')
        ->get();
        
        // dd($liststudents[0]->telno);
        foreach($liststudents as $liststudent){
            $liststudent->telno = str_replace('+6', '', $liststudent->telno);
        }

        // dd($liststudents);

        return $liststudents;
    }

    public function headings(): array
    {
        return [
            'nama',
            'jantina',
            'nama_penjaga',
            'no_ic_penjaga',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT, // Column D represents the telephone numbers (change as needed)
        ];
    }
}
