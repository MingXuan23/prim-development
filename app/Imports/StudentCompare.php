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
        $this->defaultClassId = $id;
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
        set_time_limit(300);
        if(!isset($row['nama']) || !isset($row['nama_penjaga']) || !isset($row['jantina']) || !isset($row['no_ic_penjaga'])){
            
            if($row['nama']==null &&$row['nama_penjaga']==null &&$row['jantina']==null &&$row['no_ic_penjaga']==null){
               return null;
            }
            else{
                //dd("I stop");
                throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
            }
        }

        if(!isset($row['kelas'])){
            $this->class_id = $this->defaultClassId;
        }
        else{
            $tempClass = DB::table('classes as c')
                        ->join('class_organization as co','co.class_id','c.id')
                        ->where([
                            ['c.nama',$row['kelas']],
                            ['co.organization_id',$this->oid]
                            ])
                        ->select('c.*')
                        ->first();
            
            $this->class_id = $tempClass?$tempClass->id:$this->defaultClassId;
        }

        $new_class = DB::table('classes')->where('id',$this->class_id)->first();

        $phone = trim((string)$row['no_ic_penjaga']);
        
        $phone = str_replace('-', '', $phone);
        $phone= preg_replace('/^\s+|\s+$/u', '',$phone);
        $studentName=preg_replace('/^\s+|\s+$/u', '',trim(strtoupper($row["nama"])));
        $parentName=preg_replace('/^\s+|\s+$/u', '',trim(strtoupper($row['nama_penjaga'])));
        
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
                            ->where('s.nama', 'LIKE', '%' . $studentName . '%')
                            ->where(function($query) use ($phone) {
                                $query
                                      ->Where('u.telno', $phone)
                                      ->orWhere('u.icno', $phone);
                            })
                            ->select('s.id as studentId','s.nama as studentName','s.gender as gender','u.name as parentName','u.id as parentId','u.telno as parentTelno')
                            ->first();
                            //dd(count($findStudent));
        if($findStudent!=null){
            
            $sameOrgStudent=DB::table('class_student as cs')
                            ->join('class_organization as co','co.id','cs.organclass_id')
                            //->join('organizations as o','o.id','co.organizaiton_id')
                            ->where('cs.student_id', $findStudent->studentId)
                            ->where('cs.status', 1)
                            ->where('co.organization_id',$this->oid)
                            ->select('co.class_id')
                            ->get();
            if(count($sameOrgStudent)>0)
            {
                $sameClassStudent=$sameOrgStudent->where('class_id',$this->class_id);
                if(count($sameClassStudent)>0){
                    $findStudent->newClassName=$new_class->nama;
                    $this->sameClassStudents[]=$findStudent;//this student is still at same class
                }
                else{
                    $findStudent->newClass=$this->class_id;
                    $oldClass=DB::table('classes as c')->where('c.id' ,$sameOrgStudent->first()->class_id)->first();
                    $findStudent->oldClassId=$oldClass->id;
                    $findStudent->oldClassName=$oldClass->nama;
                    $findStudent->newClassName=$new_class->nama;

                    $this->differentClassStudents[]=$findStudent;//this student is still at same org but diffretn class
                }
            }
            else{
                
                $sameOrgStudent=DB::table('class_student as cs')
                            ->join('class_organization as co','co.id','cs.organclass_id')
                            //->join('organizations as o','o.id','co.organizaiton_id')
                            ->where('cs.student_id', $findStudent->studentId)
                            ->where('cs.status', 1)
                            //->where('co.organization_id',$this->oid)
                            ->select('co.class_id','co.organization_id')
                            ->get();
                $findStudent->newClass=$this->class_id;
                $oldClass=DB::table('classes as c')->where('c.id' ,$sameOrgStudent->first()->class_id)->first();
                $oldOrgName = DB::table('organizations as o')->where('o.id',$sameOrgStudent->first()->organization_id)->first()->nama;
                $findStudent->oldClassId=$oldClass->id;
                $findStudent->oldClassName=$oldClass->nama;
                $findStudent->oldOrgName = $oldOrgName;
                $findStudent->newClassName=$new_class->nama;
                $this->differentOrgStudents[]=$findStudent; // this student exist but in different school
            }
        }
        else{
            //dd($findStudent);
            $newStudentData = new stdClass();
            $newStudentData->studentName = $studentName;
            $newStudentData->gender = $row["jantina"];
            $newStudentData->parentName =$parentName  ;
            $newStudentData->parentTelno = $phone;
            $newStudentData->classId = $this->class_id;
            $newStudentData->newClassName=$new_class->nama;

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