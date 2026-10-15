<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionRegistration extends Model
{
    protected $table = 'competition_registration';

    public function transaction()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_competition', 'registerId', 'transactionId');
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competitionId', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId', 'id');
    }
}
