<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Competition extends Model
{
    public $timestamps = false;
    protected $fillable = ['competitionTitle', 'category', 'venue', 'registerOpen', 'registerClose',
                           'competitionStart', 'competitionEnd', 'minimumAge', 'maximumAge', 'nationalFees',
                           'internationalFees', 'participateType', 'minimumParticipate', 'maximumParticipate',
                           'description', 'imagePoster', 'status', 'userId'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function participant_registration()
    {
        return $this->belongsToMany(Team::class, 'competition_registration', 'competitionId', 'teamId')->withPivot('statusPayment', 'registeredDate');
    }
}
