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

class SuggestSong implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $track_uri, $party, $bool;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($party, $track_uri, $bool)
    {
        $this->party = $party;
        $this->track_uri = $track_uri;
        $this->bool = $bool;
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
        return 'song.suggested';
    }

   public function broadcastWith(){
       /* Se suggested è true significa che la canzone è stata suggerita, se false significa
           il partecipante ha rimosso il suggerimento
       */
       return [
           'user_id' => Auth::id(),
           'track_uri' => $this->track_uri,
           'suggested' => $this->bool,
       ];
   }

}
