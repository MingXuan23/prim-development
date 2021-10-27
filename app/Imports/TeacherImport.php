<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\User;
use App\Models\OrganizationRole;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class TeacherImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function __construct($organId)
    {
        $this->organId = $organId;        
    }

    public function rules(): array
    {
        return [
            'no_kp' => [
                'required',
                // Rule::unique('users', 'icno')
            ],
            'email' => [
                'required',
                // Rule::unique('users', 'email')
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            // 'no_kp.unique' => 'Terdapat maklumat guru yang telah wujud',
            'no_kp.required' => 'Maklumat guru diperlukan',
            // 'email.unique' => 'Terdapat maklumat guru yang telah wujud',
            'email.required' => 'Maklumat guru diperlukan',
        ];
    }

    public function model(array $row)
    {
        // check if parent role exists
        $ifExits = DB::table('users as u')
                    ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
                    ->where('ou.role_id', '=', '6')
                    ->where('u.email', '=', "{$row['email']}")
                    ->where('u.icno', '=', "{$row['no_kp']}")
                    ->where('u.telno', '=', "{$row['no_tel_bimbit']}")
                    ->get();
        
        if(count($ifExits) == 0) // if not teacher
        {
            $newteacher = new Teacher([
                //
                'name'      => $row['nama'],
                'icno'      => $row['no_kp'],
                'email'     => $row['email'],
                'telno'     => $row['no_tel_bimbit'],
                'password'  => Hash::make('abc123'),
    
            ]);
    
            $newteacher->save();
        }          
        else
        {
            $newteacher = DB::table('users')
                        ->where('email', '=', "{$row['email']}")
                        ->first();
        }

        $username = DB::table('users')
            ->where('id', $newteacher->id)
            ->update(
                [
                    'username' => 'GP' . str_pad($newteacher->id, 5, "0", STR_PAD_LEFT),
                ]
            );

        DB::table('organization_user')->insert([
            'organization_id' => $this->organId,
            'user_id'       => $newteacher->id,
            'role_id'       => 5,
            'start_date'    => now(),
            'status'        => 0,
        ]);

        $user = User::find($newteacher->id);

        // role parent
        $rolename = OrganizationRole::find(5);
        $user->assignRole($rolename->nama);
    }
}
