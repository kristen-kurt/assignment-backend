<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;


class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source_id'
    ];
    public function articles(){
        return $this->hasMany(Article::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_category');
    }
    public function sources()
    {
        return $this->belongsToMany(Source::class);
    }
}
