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

class AssignFeeByParentIncome implements ToModel, WithValidation, WithHeadingRow
{
    public function __construct($oid,$fee1_id,$fee2_id,$income)
    {
       
        $this->oid = $oid;
        $this->fee1_id = $fee1_id;
        $this->fee2_id = $fee2_id;
        $this->income = $income;
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



        $parentIncome = $row['pendapat'];
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
                            ->where(function($query) use ($parentName, $phone) {
                                $query->where('u.name', $parentName )
                                      ->orWhere('u.telno', $phone)
                                      ->orWhere('u.icno', $phone);
                            })
                            ->select('s.id as studentId','s.nama as studentName','s.gender as gender','u.name as parentName','u.id as parentId','u.telno as parentTelno')
                            ->first();
                            //dd(count($findStudent));
        $organization = DB::table('organizations')->where('id',$this->oid)->first();
        $this->oid = ($organization->parent_org == null)?$this->oid:$organization->parent_org;
        $list_co =  DB::table('class_organization')->where('organization_id',$this->oid)->select('id')->get()->pluck('id');

        $class_student = DB::table('class_student')->whereIn('organclass_id',$list_co)->where('student_id' ,$findStudent->studentId)->where('status',1)->first();

        if($class_student == null){
            return null;
        }

        if($parentIncome >= $this->income){
            $fees = $this->fee1_id;
        }else{
            $fees = $this->fee2_id;

        }

        $fees_student = DB::table('class_student')
                ->where('id', $class_student->id)
                ->update(['fees_status' => 'Not Complete']);

            // DB::table('student_fees_new')->insert([
            //     'status' => 'Debt',
            //     'fees_id' => $fees,
            //     'class_student_id' => $list[$i]->class_student_id,
            // ]);

        $existingFee = DB::table('student_fees_new')
        ->where('class_student_id', $class_student->id)
        ->where('fees_id', $fees)
        ->first();

        if($existingFee == null){
            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $class_student->id,
            ]);
        }
        
        
    }


    public function startsWith($string, $startString) {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}