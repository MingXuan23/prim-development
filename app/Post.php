<?php

namespace App;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        "title",
        "content",
        "media_type",
        "media_url",
        "created_at",
        "updated_at",
        "user_id",
        "post_id",
        "shared_post_id",
        "root_shared_post_id",
        "source_name",
        "source_id",
        "likes_count",
        "comments_count",
        "shares_count"
    ];

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function saves()
    {
        return $this->hasMany(Save::class);
    }

    public function shares()
    {
        return $this->hasMany(Post::class, "shared_post_id");
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

    public function root_shared_post()
    {
        return $this->belongsTo(Post::class, "root_shared_post_id");
    }

    public function source()
    {
        return $this->morphTo("source", "source_name", "source_id");
    }
}
