<?php

namespace App\models;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //
    protected $table = 'users';

    protected $fillable = ['name', 'icno', 'email', 'password', 'telno', 'remember_token'];

    public function getOrganizationByUserId($id)
    {
        $oid = Organization::with(["user"])->whereHas('user', function ($query) use ($id) {
            $query->where("users.id", $id);
        })->get();

        dd($oid);
        return $oid;
    }
}
