<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserParticipatesParty extends Model
{

    public function user(){
        return $this->hasOne('App\User');
    }
    public function party(){
        return $this->hasOne('App\Party');
    }
    public function user_votes(){
        return $this->hasOne('App\Track');
    }
}
