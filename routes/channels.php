<?php

use Illuminate\Support\Facades\Broadcast;
use App\Broadcasting\PartyChannel;
use App\Broadcasting\SyncronizeChannel;
use App\Broadcasting\ManagementChannel;


/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('party.{partyCode}', PartyChannel::class);

Broadcast::channel('party-sync.{user_id}', SyncronizeChannel::class);

Broadcast::channel('party.{partyCode}.{user_id}', ManagementChannel::class);


