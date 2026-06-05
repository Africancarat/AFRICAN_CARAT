<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationArticle extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'hero_image',
        'body', 'meta_description', 'published', 'sort_order',
    ];

    protected $casts = ['published' => 'boolean'];

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
