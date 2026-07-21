<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ["content", "created_at", "updated_at", "data", "souce_name", "souce_id", "user_id"];
}
