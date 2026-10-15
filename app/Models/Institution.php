<?php

namespace App\Models;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    public $timestamps = false;
    public $fillable = ['instituteName', 'instituteType', 'instituteLogo'];

    public function participant()
    {
        return $this->hasMany(Participant::class, 'institutionId');
    }
}
