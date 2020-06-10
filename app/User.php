<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** RELATIONSHIPS **/
    public function parties(){
        return $this->hasMany('App\Party');
    }

    public function participates(){
        return $this->belongsToMany('App\Party','user_participates_parties')
                                    ->withPivot([
                                        'vote',
                                        'timestamp_kick',
                                        'kick_duration',
                                        'skip',
                                    ])
                                    ->withTimestamps();
    }

    public function bans(){
        return $this->belongsToMany('App\User','user_ban_users','user_id','ban_user_id')->withTimestamps();
    }



}
