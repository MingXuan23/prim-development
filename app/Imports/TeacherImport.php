<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
    public function rules(): array
    {
        return [
            'no_kp' => [
                'required',
                Rule::unique('users', 'icno')
            ],
            'eamil' => [
                'required',
                Rule::unique('users', 'email')
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'no_kp.unique' => 'Terdapat maklumat guru yang telah wujud',
            'no_kp.required' => 'Maklumat guru diperlukan',
            'email.unique' => 'Terdapat maklumat guru yang telah wujud',
            'email.required' => 'Maklumat guru diperlukan',
        ];
    }

    public function model(array $row)
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

        $username    = DB::table('users')
            ->where('id', $newteacher->id)
            ->update(
                [
                    'username' => 'GP' . str_pad($newteacher->id, 5, "0", STR_PAD_LEFT),
                ]
            );


        $userid     = Auth::id();

        // amik sekolah id untuk guru
        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        DB::table('organization_user')->insert([
            'organization_id' => $list->schoolid,
            'user_id'       => $newteacher->id,
            'role_id'       => 5,
            'start_date'    => now(),
            'status'        => 0,
        ]);
    }
}
