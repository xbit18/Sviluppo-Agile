<?php

namespace App\Events;
use App\Party;
use Illuminate\Support\Facades\Auth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MusicPaused implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $party;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Party $party)
    {
      $this->party = $party;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('party.'.$this->party->code);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'music.paused';
    }

    public function broadcastWhen(){
        $user_id = Auth::id();
        return $this->party->user->id === $user_id;
    }
}
