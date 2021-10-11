<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $organId, $kelasId;

    public function __construct($organId, $kelasId)
    {
        $this->organId = $organId;
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        //dd($this->organId, $this->kelasId);
        // return Student::all();

        // $userid = Auth::id();

        // $school = DB::table('organizations')
        //     ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
        //     ->select('organizations.id as schoolid')
        //     ->where('organization_user.user_id', $userid)
        //     ->first();

        $liststudent = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.nama as studentname', 'students.icno', 'classes.nama as classname', 'students.email')
            ->where([
                ['class_organization.organization_id', $this->organId],
                ['classes.id', $this->kelasId],
                ['class_student.status', 1]
            ])
            ->orderBy('classes.nama')
            ->get();

        // dd($liststudent);

        return $liststudent;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'No. Kp',
            'Kelas',
            'email'
        ];
    }
}
