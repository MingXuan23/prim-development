<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationUrl extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'status',
        'url',
        'title',
        'description',
    ];    
}
