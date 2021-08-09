<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentImport implements ToModel, WithValidation, WithHeadingRow
{
    public function __construct($class_id)
    {
        $id = DB::table('class_organization')->where('class_id', $class_id)->first();
        $this->class_id = $id;
    }

    public function rules(): array
    {
        return [
            'no_kp' => 'unique:students,icno'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'no_kp.unique' => 'Terdapat maklumat murid yang telah wujud',
        ];
    }

    public function model(array $row)
    {
        $co = DB::table('class_organization')
            ->select('id')
            ->where('class_id', $this->class_id->class_id)
            ->first();

        $student = new Student([
            'nama' => $row["nama"],
            'icno' => $row["no_kp"],
            'gender' => $row["jantina"],
            'email' => $row["email"]
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
