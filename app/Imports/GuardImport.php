<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\User;
use Illuminate\Validation\ValidationException;
use App\Models\OrganizationRole;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class GuardImport implements ToModel, WithHeadingRow
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

    // public function rules(): array
    // {
    //     return [
    //         'no_tel_bimbit' => [
    //             'required',
    //             Rule::unique('users', 'telno')
    //         ],
    //         'email' => [
    //             'required',
    //             Rule::unique('users', 'email')
    //         ],
    //     ];
    // }

    // public function customValidationMessages()
    // {
    //     return [
    //         // 'no_kp.unique' => 'Terdapat maklumat guru yang telah wujud',
    //         'no_tel_bimbit.required' => 'Maklumat guard diperlukan',
    //         'email.unique' => 'Terdapat maklumat guard yang telah wujud',
    //         'email.required' => 'Maklumat guard diperlukan',
    //     ];
    // }

    public function model(array $row)
    {
        // if (!isset($row['nama']) || !isset($row['email']) || !isset($row['no_tel_bimbit'])) {
        //     throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
        // }
        if(isset($row['email']) && isset($row['no_tel_bimbit']) && isset($row['nama']))
        {
            $phone = trim((string)$row['no_tel_bimbit']);

            if(strlen($phone) < 10 || strlen($phone) > 13)
            {
                // dd(strlen($phone) < 10 ,strlen($phone), strlen($phone) > 13);
                throw ValidationException::withMessages(["error" => "Invalid phone number1"]);
                

            }


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
                throw ValidationException::withMessages(["error" => "Invalid phone number2"]);
            }

    
        
             // check if parent role exists
            $ifExists = DB::table('users as u')
            // ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
            // ->where('ou.role_id', '=', '6')
            ->where('u.email', '=', "{$row['email']}")
            // ->where('u.telno', '=', "{$row['no_tel_bimbit']}")
            ->get();

            if (count($ifExists) == 0) // if not parent
            {
                $newteacher = new Teacher([
                    //
                    'name'      => $row['nama'],
                    // 'icno'      => $row['no_kp'],
                    'email'     => $row['email'],
                    'telno'     => $phone,
                    'password'  => Hash::make('abc123'),

                ]);

                $newteacher->save();
            } else {
                $newteacher = DB::table('users')
                    ->where('email', '=', "{$row['email']}")
                    ->first();
            }

            $username = DB::table('users')
                ->where('id', $newteacher->id)
                ->update(
                    [
                        'name'  =>  $row['nama'],
                        'telno' =>  $phone,
                        'username' => 'GS' . str_pad($newteacher->id, 5, "0", STR_PAD_LEFT),
                    ]
                );
            
                // check if this user ady have role guard
            $check_role = DB::table('organization_user')
                            ->where('organization_id',$this->organId)
                            ->where('user_id',$newteacher->id)
                            ->where('role_id',14)
                            ->first();

            if($check_role == null)
            {
                DB::table('organization_user')->insert([
                    'organization_id' => $this->organId,
                    'user_id'       => $newteacher->id,
                    'role_id'       => 14,
                    'start_date'    => now(),
                    'status'        => 0,
                ]);
    
                $user = User::find($newteacher->id);
    
                // role parent
                $rolename = OrganizationRole::find(14);
                $user->assignRole($rolename->nama);
            }

           
        }
       
    }

    public function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}
