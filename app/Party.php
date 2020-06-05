<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
   protected $guarded = [];

   /** RELATIONSHIPS **/

   public function users(){
      return $this->belongsToMany('App\User','user_participates_parties');
   }

   public function user(){
      return $this->belongsTo('App\User');
   }

   public function genre(){
      return $this->belongsToMany('App\Genre','party_has_genre');
   }

   public function tracks(){
      return $this->hasMany('App\Track');
   }

}
