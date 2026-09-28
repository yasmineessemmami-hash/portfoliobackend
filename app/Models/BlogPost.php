<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 
        'slug', 
        'content', 
        'image', 
        'media_type', 
        'category', 
        'tags', 
        'author_id', 
        'published_at', 
        'is_featured'
    ];

    protected $casts = [
        'content' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'author_id' => 'integer'
    ];

    public function author()
    {
        return $this->belongsTo(BlogAuthor::class, 'author_id');
    }
}
