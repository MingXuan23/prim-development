<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;

class StudentImport implements ToModel
{
    public function  __construct($class_id)
    {
        $id = DB::table('class_organization')->where('class_id', $class_id)->first();
        $this->class_id = $id;
    }

    public function model(array $row)
    {

        $co = DB::table('class_organization')
            ->select('id')
            ->where('class_id', $this->class_id->class_id)
            ->first();

        $student = new Student([
            'nama' => $row[0],
            'icno' => $row[1],
        ]);

        $student->save();
        // id kelas
        DB::table('class_student')->insert([
            'organclass_id'   => $co->id,
            'student_id'      => $student->id,
            'start_date'      => now(),
            'status'          => 1,
        ]);
    }
}
