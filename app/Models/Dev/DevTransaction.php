<?php

namespace App\Models\Dev;

use App\Models\Transaction;

class DevTransaction extends Transaction
{
    public $connection = 'mysql_dev';
    public $table = 'primmy_dev.transactions';
}
