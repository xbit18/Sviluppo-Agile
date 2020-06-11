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
use Illuminate\Support\Facades\Auth;
use App\Party;


class VoteEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $party, $track;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($party, $track)
    {
        $this->party = $party;
        $this->track = $track;
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

    public function broadcastAs(){
        return 'song.voted';
    }

    public function broadcastWith(){
        return [
            'song_id' => $this->track->id,
            'likes' => $this->track->votes,
        ];
    }

    /** @return bool **/

    public function broadcastWhen(){
        
        $user = Auth::user();
        $bool = $user->participates()->where('party_id',$this->party->id)->first()->pivot->vote === NULL;
        return $bool;
      
    }

}
