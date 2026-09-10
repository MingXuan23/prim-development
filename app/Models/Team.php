<?php

namespace App\Models;

use App\models\Competition;
use App\models\Participant;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public $timestamps = false;
    public $fillable = ['groupName'];

    public function team_registration()
    {
        return $this->belongsToMany(Competition::class, 'competition_registration', 'teamId', 'competitionId')->withPivot('statusPayment', 'registeredDate');
    }

    public function participant()
    {
        return $this->hasMany(Participant::class, 'teamId');
    }
}
