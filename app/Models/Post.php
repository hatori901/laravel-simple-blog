<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'slug', 'title', 'content', 'published_at', 'is_draft'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_draft', 0)->where('published_at', '<=', now());
    }

    public function status()
    {
        if ($this->is_draft) return 'Draft';
        if ($this->published_at && $this->published_at->isFuture()) return 'Scheduled';
        return 'Published';
    }
}