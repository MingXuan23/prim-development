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
    private $status = 0;

    public function __construct($dorm_id)
    {
        $selected_dorm = DB::table('dorms')->where('id', $dorm_id)->first();
        $this->dorm_id = $selected_dorm->id;
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

        if (!isset($row['name']))
            throw ValidationException::withMessages(["error" => "missing name"]);
        else if (!isset($row['no_tel_bimbit_penjaga']))
            throw ValidationException::withMessages(["error" => "missing no_tel_bimbit_penjaga"]);
        else if (!isset($row['class']))
            throw ValidationException::withMessages(["error" => "missing class"]);

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

        // dd("123");
        //this is step 4
        $student_id = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->select('students.id')
            ->where('students.nama', $row['name'])
            ->where('students.email', $row['email'])
            ->where('students.parent_tel', '=', "{$phone}")
            ->whereNull('class_student.dorm_id')
            ->value('students.id');
        //if the student is already inside a dorm
        if (!isset($student_id)) {
            return redirect('/dorm/dorm/indexDorm')->with('fail', 'Residents have not been added successfully because the student added is already inside a dorm');
        } else {
            $checkNoStudentInsideDorm = DB::table('class_student as cs')
                ->where('cs.dorm_id', '=', $this->dorm_id)
                ->count();

            $checkCapacity = DB::table('dorms')
                ->where('id', '=', $this->dorm_id)
                ->value('accommodate_no');

            //if number of student inside the dorm is less than capacity
            if ($checkNoStudentInsideDorm <= $checkCapacity) {
                //let this student come in the dorm
                $result = DB::table('class_student as cs')
                    ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                    ->where([
                        ['cs.student_id', '=', $student_id],
                        ['cs.organclass_id', '=', $co_id->id],
                        ['cs.status', '=', 1],
                    ])
                    ->update(
                        [
                            'cs.dorm_id' => $this->dorm_id,
                            'cs.start_date_time' => now()->toDateTimeString(),
                            'cs.end_date_time' => null
                        ]
                    );
                //if successfully update the dorm id and start date time to the class student
                if ($result) {
                    //update dorm number
                    $this->status++;
                    DB::table('dorms')
                        ->where('id', $this->dorm_id)
                        ->update(['student_inside_no' => $this->status]);
                } else {
                    return redirect('/dorm/dorm/indexDorm')->with('fail', 'Residents have not been added successfully because the student status is not active');
                }
            }
        }
    }

    public function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}
