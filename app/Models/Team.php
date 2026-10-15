<?php

namespace App\Models;

use App\Models\Competition;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public $timestamps = false;
    public $fillable = ['groupName'];

    public function team_registration()
    {
        return $this->belongsToMany(Competition::class, 'competition_registration', 'teamId', 'competitionId')->withPivot('id', 'statusPayment', 'registeredDate', 'winnerPlacement', 'certificateSelected');
    }

    public function participant()
    {
        return $this->hasMany(Participant::class, 'teamId');
    }
}
