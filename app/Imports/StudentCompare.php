<?php

namespace App\Imports;
use stdClass;
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

class StudentCompare implements ToModel, WithValidation, WithHeadingRow
{
    public function __construct($class_id)
    {
        $id = DB::table('class_organization')->where('class_id', $class_id)->first()->class_id;
        $this->class_id = $id;
        $this->oid = DB::table('class_organization')
        ->where('class_id', $this->class_id)
        ->first()->organization_id;

        
        $this->sameClassStudents= [];
        $this->differentClassStudents= [];
        $this->differentOrgStudents= [];
        $this->newStudents=[];
    }

    public function rules(): array
    {
        return [
            /* 'no_kp' => [
                'required',
                 Rule::unique('students', 'icno')
            ], */
        ];
    }

    public function customValidationMessages()
    {
        return [
            /* 'no_kp.unique' => 'Terdapat maklumat murid yang telah wujud',
            'no_kp.required' => 'Maklumat murid diperlukan', */
        ];
    }

    public function model(array $row)
    {
        if(!isset($row['nama']) || !isset($row['nama_penjaga']) || !isset($row['jantina']) || !isset($row['no_tel_bimbit_penjaga'])){
            throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
        }

        $phone = trim((string)$row['no_tel_bimbit_penjaga']);

        if(!$this->startsWith($phone,"+60") && !$this->startsWith($phone,"60")){
            if(strlen($phone) == 10) {
                $phone = str_pad($phone, 12, "+60", STR_PAD_LEFT);
            } 
            elseif(strlen($phone) == 11)
            {
                $phone = str_pad($phone, 13, "+60", STR_PAD_LEFT);
            }   
        } else if($this->startsWith($phone,"60")){

            if(strlen($phone) == 11) {
                $phone = str_pad($phone, 12, "+60", STR_PAD_LEFT);
            } 
            elseif(strlen($phone) == 12)
            {
                $phone = str_pad($phone, 13, "+60", STR_PAD_LEFT);
            } 
        }
        elseif($this->startsWith($phone,"+60")) {
            // do nothing
        }
        else{
            throw ValidationException::withMessages(["error" => "Invalid phone number"]);
        }

        //$class = ClassModel::find($this->class_id->class_id);
        
        // $gender = (int) substr($row["no_kp"], -1) % 2 == 0 ? "P" : "L";
        //$gender = $row["jantina"];

        $findStudent=DB::table('students as s')
                            ->join('organization_user_student as ous','ous.student_id','s.id')
                            ->join('organization_user as ou','ou.id','ous.organization_user_id')
                            ->join('users as u','u.id','ou.user_id')
                            ->where('s.nama', 'LIKE', '%' . $row['nama'] . '%')
                            ->where('u.name','LIKE', '%' . $row['nama_penjaga'] . '%')
                            ->where('u.telno',$phone)
                            ->select('s.id as studentId','s.nama as studentName','s.gender as gender','u.name as parentName','u.id as parentId','u.telno as parentTelno')
                            ->first();
                            //dd(count($findStudent));
        if($findStudent!=null){
            
            $sameOrgStudent=DB::table('class_student as cs')
                            ->join('class_organization as co','co.id','cs.organclass_id')
                            //->join('organizations as o','o.id','co.organizaiton_id')
                            ->where('cs.student_id', $findStudent->studentId)
                            ->where('co.organization_id',$this->oid)
                            ->select('co.class_id')
                            ->get();
            if(count($sameOrgStudent)>0)
            {
                $sameClassStudent=$sameOrgStudent->where('class_id',$this->class_id);
                if(count($sameClassStudent)>0){
                    $this->sameClassStudents[]=$findStudent;//this student is still at same class
                }
                else{
                    $this->differentClassStudents[]=$findStudent;//this student is still at same org but diffretn class
                }
            }
            else{
                $this->differentOrgStudents[]=$findStudent; // this student exist but in different school
            }
        }
        else{
            $newStudentData = new stdClass();
            $newStudentData->studentName = strtoupper($row["nama"]);
            $newStudentData->gender = $row["jantina"];
            $newStudentData->parentName = strtoupper($row['nama_penjaga']);
            $newStudentData->parentTelno = $phone;
            $newStudentData->classId = $this->class_id;


            $this->newStudents[]=$newStudentData;//new student
        }
       
        
        
    }

    public function getStudentArray()
    {
        return[
            'sameClassStudents' => $this->sameClassStudents,
            'differentClassStudents' => $this->differentClassStudents,
            'differentOrgStudents' => $this->differentOrgStudents,
            'newStudents' => $this->newStudents,
        ];
        //dd($this->studentArray);
    }

    public function startsWith($string, $startString) {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}