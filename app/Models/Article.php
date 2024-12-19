<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'article_title',
        'category',
        'url',
        'image_url',
        'published_at',
    ];
}

