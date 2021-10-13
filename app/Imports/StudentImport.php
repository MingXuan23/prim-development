<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Parents;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\OrganizationRole;
use App\User;
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
            'no_kp' => [
                'required',
                 Rule::unique('students', 'icno')
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'no_kp.unique' => 'Terdapat maklumat murid yang telah wujud',
            'no_kp.required' => 'Maklumat murid diperlukan',
        ];
    }

    public function model(array $row)
    {
        $co = DB::table('class_organization')
            ->select('id', 'organization_id as oid')
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

        $parent = DB::table('users')
            ->select()
            ->where('email', $row['email_penjaga'])
            ->first();
        
        if(is_null($parent))
        {
            $parent = new Parents([
                'name'           =>  strtoupper($row['nama_penjaga']),
                // 'icno'           =>  $row['no_kp_penjaga'],
                'email'          =>  $row['email_penjaga'],
                'password'       =>  Hash::make('abc123'),
                'telno'          =>  $row['no_tel_bimbit_penjaga'],
                'remember_token' =>  Str::random(40),
            ]);
            $parent->save();
        }

        DB::table('organization_user')->insert([
            'organization_id'   => $co->oid,
            'user_id'           => $parent->id,
            'role_id'           => 6,
            'start_date'        => now(),
            'status'            => 1,
        ]);

        $ou = DB::table('organization_user')
                    ->where('user_id', $parent->id)
                    ->where('organization_id', $co->oid)
                    ->where('role_id', 6)
                    ->first();

        $user = User::find($parent->id);
        // role parent
        $rolename = OrganizationRole::find(6);
        $user->assignRole($rolename->nama);

        DB::table('organization_user_student')
                ->insert([
                    'organization_user_id'  => $ou->id,
                    'student_id'            => $student->id
                ]);
            
        DB::table('students')
            ->where('id', $student->id)
            ->update(['parent_tel' => $parent->telno]);
    }
}
