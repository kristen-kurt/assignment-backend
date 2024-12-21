<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
    public function articles(){
        return $this->hasMany(Article::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_source');
    }
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function authors()
    {
        return $this->hasMany(Author::class);
    }
}
