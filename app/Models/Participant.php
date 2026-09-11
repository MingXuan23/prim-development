<?php

namespace App\Models;

use App\models\Team;
use App\models\Institution;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Participant extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'icNo', 'email', 'noTel', 'gender', 'currentGrade','supervisorName', 
                           'supervisorEmail', 'supervisorNoTel', 'isLeader', 'teamId', 'userId', 'institutionId'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institutionId');
    }
}
