<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use App\Party;

class PlayerPlayed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $party, $track_uri, $position_ms;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Party $party, $track_uri, $position_ms)
    {
        $this->party = $party;
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
        return new PresenceChannel('party.'.$this->party->code);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'player.played';
    }

    public function broadcastWhen(){
        $user_id = Auth::id();
        return $this->party->user->id === $user_id;
    }
}
