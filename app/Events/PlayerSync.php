<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerSync implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $party, $track_uri, $position_ms, $user_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($party, $user_id, $track_uri, $position_ms)
    {
        $this->party = $party;
        $this->user_id = $user_id;
        $this->track_uri = $track_uri;
        $this->position_ms = $position_ms;
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('party-sync.'.$this->user_id);
    }

    public function broadcastAs(){
        return 'player.syncronize';
    }

    public function broadcastWith(){
        return [
            'track_uri' => $this->track_uri,
            'position_ms' => $this->position_ms,
        ];
    }

    public function broadcastWhen(){
        $host = Auth::user();
        return $this->party->user->id === $host->id;
    }
}
