<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class TeacherImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $newteacher = new Teacher([
            //
            'name'      => $row[0],
            'icno'      => $row[1],
            'email'     => $row[2],
            'telno'     => $row[3],
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
            'role_id'       => 2,
            'start_date'    => now(),
            'status'        => 0,
        ]);
    }
}
