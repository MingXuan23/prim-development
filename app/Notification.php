<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ["content", "type", "created_at", "updated_at", "data", "user_id"];
}
