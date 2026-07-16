<?php

namespace App;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ["title", "content", "media_type", "media_url", "created_at", "updated_at", "user_id", "post_id", "shared_donation_id"];

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function saves()
    {
        return $this->hasMany(Save::class);
    }

    public function parent()
    {
        return $this->belongsTo(Post::class, "post_id");
    }

    public function comments()
    {
        return $this->hasMany(Post::class, "post_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donation_post()
    {
        return $this->belongsTo(Donation::class, "shared_donation_id");
    }
}
