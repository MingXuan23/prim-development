<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class DonationTransaction extends Model
{
    public $table = "donation_transaction";

    public $timestamps = false;
}
