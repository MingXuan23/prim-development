<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Parents;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\OrganizationRole;
use App\User;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ResidentImport implements ToModel, WithValidation, WithHeadingRow
{
    public function __construct($dorm_id, $number_student_inside)
    {
        $selected_dorm = DB::table('dorms')->where('id', $dorm_id)->first();
        $this->dorm_id = $selected_dorm->id;
        $this->number_student_inside = $number_student_inside;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required'
                //Rule::unique('students', 'icno')
            ],
            'email' => [
                'required'
            ],
            // 'nama_penjaga' => [
            //     'required'
            // ],
            'no_tel_bimbit_penjaga' => [
                'required'
                //Rule::unique('users', 'telno')
            ],
            'class' => [
                'required'
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Maklumat nama murid diperlukan',
            //'nama_penjaga.required' => 'Maklumat nama penjaga diperlukan',
            'no_tel_bimbit_penjaga.required' => 'Maklumat nombor telefon bimbit penjaga diperlukan',
            'class.required' => 'Maklumat kelas murid diperlukan'
        ];
    }

    public function model(array $row)
    {


        //validate column name
        // if (!isset($row['name']) || !isset($row['nama_penjaga']) || !isset($row['no_tel_bimbit_penjaga']) || !isset($row['class'])) {
        //     throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
        // }

        if (!isset($row['name']))
            throw ValidationException::withMessages(["error" => "missing name"]);
        // else if (!isset($row['nama_penjaga']))
        //     throw ValidationException::withMessages(["error" => "missing nama_penjaga"]);
        else if (!isset($row['no_tel_bimbit_penjaga']))
            throw ValidationException::withMessages(["error" => "missing no_tel_bimbit_penjaga"]);
        else if (!isset($row['class']))
            throw ValidationException::withMessages(["error" => "missing class"]);
        // else
        //     throw ValidationException::withMessages(["error" => "missing idunnoe"]);


        //validate phone number
        $phone = trim((string)$row['no_tel_bimbit_penjaga']);

        if (!$this->startsWith($phone, "+60") && !$this->startsWith($phone, "60")) {
            if (strlen($phone) == 10) {
                $phone = str_pad($phone, 12, "+60", STR_PAD_LEFT);
            } elseif (strlen($phone) == 11) {
                $phone = str_pad($phone, 13, "+60", STR_PAD_LEFT);
            }
        } else if ($this->startsWith($phone, "60")) {

            if (strlen($phone) == 11) {
                $phone = str_pad($phone, 12, "+60", STR_PAD_LEFT);
            } elseif (strlen($phone) == 12) {
                $phone = str_pad($phone, 13, "+60", STR_PAD_LEFT);
            }
        } elseif ($this->startsWith($phone, "+60")) {
            // do nothing
        } else {
            throw ValidationException::withMessages(["error" => "Invalid phone number"]);
        }


        //Step 1: get organization id by dorm id
        //Step 2: get class id from class name
        //Step 3: then get class organization id where organizatioin id= organization id AND class id = class id
        //Step 4: then get student id by matching name and parent_tel
        //Step 5: check the student import is mmg not inside any dorm

        //this is step 1
        $get_organization_id = DB::table('dorms')
            ->select('organization_id')
            ->where('id', $this->dorm_id)
            ->value('organization_id');

        //this is step 2
        $class_exists = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id', 'classes.nama')
            ->where('classes.nama', '=', $row['class'])
            ->where('class_organization.organization_id', '=', $get_organization_id)
            ->get();

        //this is step 3
        //if the class is exist
        if (isset($class_exists)) {
            $that_class_id = DB::table('classes')
                ->select('id', 'nama')
                ->where('nama', '=', $row['class'])
                ->value('id');

            $co_id = DB::table('class_organization')
                ->select('id')
                ->where('organization_id', $get_organization_id)
                ->where('class_id', $that_class_id)
                ->first();
        }

        //this is step 4
        $student_id = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->select('students.id')
            ->where('students.nama', $row['name'])
            ->where('students.email', $row['email'])
            ->where('students.parent_tel', '=', "{$phone}")
            ->whereNull('class_student.dorm_id')
            ->value('students.id');

        //if the student is already inside the dorm
        if (!isset($student_id)) {
            return redirect('/dorm/dorm/indexDorm')->with('fail', 'Residents have not been added successfully because the student added is already inside a dorm');
        } else {
            $result = DB::table('class_student as cs')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->where([
                    ['cs.student_id', '=', $student_id],
                    ['cs.organclass_id', '=', $co_id->id],
                    ['cs.status', '=', 1],
                ])
                ->update(['cs.dorm_id' => $this->dorm_id]);

            //if successfully update the dorm id to the class student
            if ($result) {
                DB::table('dorms')
                    ->where('id', $this->dorm_id)
                    ->update(['student_inside_no' => $this->number_student_inside]);
            } else {
                return redirect('/dorm/dorm/indexDorm')->with('fail', 'Residents have not been added successfully because the student status is not active');
            }
        }
    }

    public function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}
