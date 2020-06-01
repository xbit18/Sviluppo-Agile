<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = ['genre'];

    public function genre(){
        return $this->belongsToMany('App\Party','party_has_genre');
     }
  
}
