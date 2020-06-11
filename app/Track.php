<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $guarded = [];
    /**
     * Foreign Keys
     */
    public function party(){
        return $this->belongsTo('App\Party');
     }
}
