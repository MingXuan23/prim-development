<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Competition extends Model
{
    public $timestamps = false;
    protected $fillable = ['competitionTitle', 'category', 'venue', 'registerOpen', 'registerClose',
                           'competitionStart', 'competitionEnd', 'minimumAge', 'maximumAge', 'nationalFees',
                           'internationalFees', 'participateType', 'winnerType', 'minParticipateGroup', 'maxParticipateGroup',
                           'totalParticipate', 'description', 'imagePoster', 'statusCompetition', 'statusResult', 'organizationId'];

    public function participant_registration()
    {
        return $this->belongsToMany(Team::class, 'competition_registration', 'competitionId', 'teamId')->withPivot('statusPayment', 'registeredDate', 'winnerPlacement', 'certificateSelected');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organizationId', 'id');
    }
}
