<?php

namespace App\Broadcasting;

use App\User;

class ManagementChannel
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
    public function join(User $user, $partyCode, $user_id)
    {
      return $user->id === (int) $user_id;
    }
}
