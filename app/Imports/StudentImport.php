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
    {   //
        //dd(!isset($row['nama']) , !isset($row['nama_penjaga']) , !isset($row['jantina']) , !isset($row['no_tel_bimbit_penjaga']));
        //dd($row);
        if(!isset($row['nama']) || !isset($row['nama_penjaga']) || !isset($row['jantina']) || !isset($row['no_tel_bimbit_penjaga'])){
            
            if($row['nama']==null &&$row['nama_penjaga']==null &&$row['jantina']==null &&$row['no_tel_bimbit_penjaga']==null){
               return null;
            }
            else{
                //dd("I stop");
                throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
            }
        }
        //dd("Success");
        $phone = trim((string)$row['no_tel_bimbit_penjaga']);
        $phone = str_replace('-', '', $phone);
        $phone= preg_replace('/^\s+|\s+$/u', '',$phone);
        
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
        
        $co = DB::table('class_organization')
        ->select('id', 'organization_id as oid')
        ->where('class_id', $this->class_id->class_id)
        ->first();

        $class = ClassModel::find($this->class_id->class_id);
        
        // $gender = (int) substr($row["no_kp"], -1) % 2 == 0 ? "P" : "L";
        $gender = $row["jantina"];
        if($gender!="L" && $gender!="P"){
            throw ValidationException::withMessages(["error" => "Invalid gender Information"]);
        }
        $ifExits = DB::table('users as u')
                    ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
                    // ->where('u.email', '=', $request->get('parent_email'))
                    // ->where('u.icno', '=', $request->get('parent_icno'))
                    ->where('u.telno', '=', $phone)
                    ->where('ou.organization_id', $co->oid)
                    ->whereIn('ou.role_id', [5, 6])
                    ->get();
        
        if(count($ifExits) == 0) { // if not teacher or parent

            $newparent = DB::table('users')
                            ->where('telno', '=', $phone)
                            ->first();
            
            if(empty($newparent))
            {   
                $validator = Validator::make($row, [
                    $phone      =>  'required|unique:users,telno',
                ]);

                $newparent = new Parents([
                    'name'           =>  preg_replace('/^\s+|\s+$/u', '',trim(strtoupper($row['nama_penjaga']))),
                    'password'       =>  Hash::make('abc123'),
                    'telno'          =>  $phone,
                    'remember_token' =>  Str::random(40),
                ]);
                $newparent->save();

            }
            
            // add parent role
            $parentRole = DB::table('organization_user')
                        ->where('user_id', $newparent->id)
                        ->where('organization_id', $co->oid)
                        ->where('role_id', 6)
                        ->first();
            
            // dd($parentRole);

            if(empty($parentRole))
            {
                DB::table('organization_user')->insert([
                    'organization_id'   => $co->oid,
                    'user_id'           => $newparent->id,
                    'role_id'           => 6,
                    'start_date'        => now(),
                    'status'            => 1,
                ]);
            }   
        }
        else {

            $newparent = DB::table('users')
                        ->where('telno', '=', "{$phone}")
                        ->first();

            // add parent role
            $parentRole = DB::table('organization_user')
                        ->where('user_id', $newparent->id)
                        ->where('organization_id', $co->oid)
                        ->where('role_id', 6)
                        ->first();
            
            // dd($parentRole);

            if(empty($parentRole))
            {
                DB::table('organization_user')->insert([
                    'organization_id'   => $co->oid,
                    'user_id'           => $newparent->id,
                    'role_id'           => 6,
                    'start_date'        => now(),
                    'status'            => 1,
                ]);
            } 
        }

        $ou = DB::table('organization_user')
                ->where('user_id', $newparent->id)
                ->where('organization_id', $co->oid)
                ->where('role_id', 6)
                ->first();

        $user = User::find($newparent->id);

        // role parent
        $rolename = OrganizationRole::find(6);
        $user->assignRole($rolename->nama);
        
        $student = new Student([
            'nama'       => preg_replace('/^\s+|\s+$/u', '',trim(strtoupper($row["nama"]))),
            // 'icno'       => $row["no_kp"],
            'gender'     => $row["jantina"],
            'email'      => isset($row['email']) ? $row['email'] : NULL,
        ]);

        $student->save();


        DB::table('class_student')->insert([
            'organclass_id'   => $co->id,
            'student_id'      => $student->id,
            'start_date'      => now(),
            'status'          => 1,
        ]);

        $classStu = DB::table('class_student')
                ->where('student_id', $student->id)
                ->first();

        DB::table('organization_user_student')->insert([
            'organization_user_id'  => $ou->id,
            'student_id'            => $student->id
        ]);
            
        DB::table('students')
            ->where('id', $student->id)
            ->update(['parent_tel' => $newparent->telno]);
        
        // check fee for new in student
        // check category A fee
        $ifExitsCateA = DB::table('fees_new')
                        ->where('category', 'Kategory A')
                        ->where('organization_id', $co->oid)
                        ->where('status', 1)
                        ->get();
        
        $ifExitsCateBC = DB::table('fees_new')
                        ->whereIn('category', ['Kategory B', 'Kategory C'])
                        ->where('organization_id', $co->oid)
                        ->where('status', 1)
                        ->get();

        if(!$ifExitsCateA->isEmpty() && count($ifExits) == 0)
        {
            foreach($ifExitsCateA as $kateA)
            {
                DB::table('fees_new_organization_user')->insert([
                    'status'                    => 'Debt',
                    'fees_new_id'               =>  $kateA->id,
                    'organization_user_id'      =>  $ou->id,
                    'transaction_id'            => NULL
                ]);
            }
        }

        if(!$ifExitsCateBC->isEmpty())
        {
            foreach($ifExitsCateBC as $kateBC)
            {
                $target = json_decode($kateBC->target);

                if(isset($target->gender))
                {
                    if($target->gender != $gender)
                    {
                        continue;
                    }
                }
                
                if($target->data == "All_Level" || $target->data == $class->levelid)
                {
                    DB::table('student_fees_new')->insert([
                        'status'            => 'Debt',
                        'fees_id'           =>  $kateBC->id,
                        'class_student_id'  =>  $classStu->id
                    ]);
                }
                else if(is_array($target->data))
                {
                    if(in_array($class->id, $target->data))
                    {
                        DB::table('student_fees_new')->insert([
                            'status'            => 'Debt',
                            'fees_id'           =>  $kateBC->id,
                            'class_student_id'  =>  $classStu->id
                        ]);
                    }
                }

            }
        }

        $child_organs = DB::table('organizations')
                    ->where('parent_org', $co->oid)
                    ->get();

        foreach ($child_organs as $child_organ) {

            $organ_user_id = DB::table('organization_user')->insertGetId([
                'organization_id'   => $child_organ->id,
                'user_id'           => $newparent->id,
                'role_id'           => 6,
                'start_date'        => now(),
                'status'            => 1,
            ]);

            $ifExitsCateA = DB::table('fees_new')
                        ->where('category', 'Kategory A')
                        ->where('organization_id', $child_organ->id)
                        ->where('status', 1)
                        ->get();
        
            $ifExitsCateBC = DB::table('fees_new')
                    ->whereIn('category', ['Kategory B', 'Kategory C'])
                    ->where('organization_id', $child_organ->id)
                    ->where('status', 1)
                    ->get();
            
            if(!$ifExitsCateA->isEmpty() && count($ifExits) == 0)
            {
                foreach($ifExitsCateA as $kateA)
                {
                    DB::table('fees_new_organization_user')->insert([
                        'status'                    => 'Debt',
                        'fees_new_id'               =>  $kateA->id,
                        'organization_user_id'      =>  $organ_user_id,
                        'transaction_id'            => NULL
                    ]);
                }
            }

            if(!$ifExitsCateBC->isEmpty())
            {
                foreach($ifExitsCateBC as $kateBC)
                {
                    $target = json_decode($kateBC->target);

                    if(isset($target->gender))
                    {
                        if($target->gender != $gender)
                        {
                            continue;
                        }
                    }
                    
                    if($target->data == "All_Level" || $target->data == $class->levelid)
                    {
                        DB::table('student_fees_new')->insert([
                            'status'            => 'Debt',
                            'fees_id'           =>  $kateBC->id,
                            'class_student_id'  =>  $classStu->id
                        ]);
                    }
                    else if(is_array($target->data))
                    {
                        if(in_array($class->id, $target->data))
                        {
                            DB::table('student_fees_new')->insert([
                                'status'            => 'Debt',
                                'fees_id'           =>  $kateBC->id,
                                'class_student_id'  =>  $classStu->id
                            ]);
                        }
                    }

                }
            }
        }
    }

    public function startsWith($string, $startString) {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}