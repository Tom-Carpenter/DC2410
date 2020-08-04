<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Order extends Model
{
    //
    use Sortable;
    public $sortable = ['id', 'email', 'total', 'updated_at'];// determines which fields this model can be sorted by
    protected $fillable = [
        'user_id', 'cost', 'purchased','total','updated_at'
    ];
    public function books(){
        return $this->belongsToMany('App\Book')->withPivot('quantity','sub_total');
    }
    public function user(){
        return $this->hasOne('App\User','id','user_id');
    }
}
