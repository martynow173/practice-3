<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'category_id', 'image'];
    protected $guarded = [];
    use SoftDeletes;

    public function categories()
    {
        return $this->belongsToMany('App\Category')->withTimestamps();
    }
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function commentsCount()
    {
        return $this->comments()
            ->selectRaw('product_id, count(*) as aggregate');
    }
}
