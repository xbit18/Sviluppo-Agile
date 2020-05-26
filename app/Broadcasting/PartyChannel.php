<?php

namespace App\Broadcasting;

use App\User;
use App\Party;

class PartyChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\User  $user
     * @return array|bool
     */
    public function join(User $user)
    {
        /** Per le party private **/
        // $party = Party::where('code',$partyCode)->first();

        // if($party->users->contains($user)){
        //         return ['id' => $user->id, 'name' => $user->name];
        // }else{
        //     return false;
        // }
        return (['user' => $user]);
    }
}
