<?php

namespace App\Imports;

use App\Models\ClassModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;

class ClassImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $newclass = new ClassModel([
            //
            'nama'      =>$row[0],
            'levelid'   =>$row[1],
        ]);

        $newclass->save();

        $userid     = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        DB::table('class_organization')->insert([
            'organization_id' => $school->schoolid,
            'class_id'        => $newclass->id,
            'start_date'      => now(),
        ]);


    }
}
