<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Source;
use App\Models\Author;


class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'article_title',
        'category_id',
        'source_id',
        'url',
        'image_url',
        'published_at',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}

