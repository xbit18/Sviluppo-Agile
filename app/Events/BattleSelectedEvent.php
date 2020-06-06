<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BattleSelectedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $track, $side, $party;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($track, $side, $party)
    {
        $this->party = $party;
        $this->track = $track;
        $this->side = $side;
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

    public function broadcastAs() {
        return 'battle.selected';
    }

    public function broadcastWith(){
        return [
            'track' => $this->track,
            'side' => $this->side
        ];
    }

}
