<?php

namespace App\Imports;

use App\Models\Parents;
use App\User;
use App\Models\OrganizationRole;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class ParentsImport implements ToModel, WithHeadingRow, WithValidation
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

    public function model(array $row)
    {
        if(!isset($row['nama']) || !isset($row['no_kp']) || !isset($row['email']) || !isset($row['no_tel_bimbit'])){
            throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
        }

        $userid     = Auth::id();
        // check if teacher role exists
        $ifExits = DB::table('users as u')
                    ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
                    ->where('ou.role_id', '=', '5')
                    ->where('u.email', '=', "{$row['email']}")
                    ->where('u.icno', '=', "{$row['no_kp']}")
                    ->where('u.telno', '=', "{$row['no_tel_bimbit']}")
                    ->get();
        
        if(count($ifExits) == 0) // if not teacher
        {
            $newparents =  new Parents([
                //
                'name'      => $row['nama'],
                'icno'      => $row['no_kp'],
                'email'     => $row['email'],
                'telno'     => $row['no_tel_bimbit'],
                'password'  => Hash::make('abc123'),
            ]);
    
            $newparents->save();
        }
        else
        {
            $newparents = DB::table('users')
                        ->where('email', '=', "{$row['email']}")
                        ->first();
        }

        DB::table('organization_user')->insert([
            'organization_id'   => $this->organId,
            'user_id'           => $newparents->id,
            'role_id'           => 6,
            'start_date'        => now(),
            'status'            => 1,
        ]);

        $user = User::find($newparents->id);

        // role parent
        $rolename = OrganizationRole::find(6);
        $user->assignRole($rolename->nama);
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
            // 'no_kp.unique' => 'Terdapat maklumat penjaga yang telah wujud',
            'no_kp.required' => 'Maklumat penjaga diperlukan',
            // 'email.unique' => 'Terdapat penjaga yang telah wujud',
            'email.required' => 'Maklumat penjaga diperlukan',
        ];
    }

}
