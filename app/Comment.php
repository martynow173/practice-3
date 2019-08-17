<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    protected $fillable = ['title', 'rate', 'description', 'product_id', 'user_id'];
    protected $guarded = [];
    use SoftDeletes;
    public function products()
    {
        $this->belongsTo('App\Product')->withTimeStamps();
    }
    public function users()
    {
        $this->belongsTo(('App\User')->withTimeStamps());
    }

}
