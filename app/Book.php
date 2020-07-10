<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Book extends Model
{
    use Sortable;
    public $sortable = ['id', 'name', 'price', 'stock']; // determines which fields this model can be sorted by
    protected $fillable = [
        'name', 'price','stock','published_year','description'
    ];

    public function authors(){
        return $this->belongsToMany('App\Author');
    }
    public function categories(){
        return $this->belongsToMany('App\Category');
    }
    public function images(){
        return $this->belongsToMany('App\Image');
    }
}
