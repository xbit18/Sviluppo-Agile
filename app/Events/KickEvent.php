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

class KickEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $party, $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($party, $user)
    {
        $this->party = $party;
        $this->user = $user;
     
       
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('party.'.$this->party->code.'.'.$this->user->id);
    }

    public function broadcastAs(){
        return 'user.kicked';
    }

    public function broadcastWith(){
        return [
            'kicked' =>true,
        ];
    }

    public function broadcastWhen(){
        $host = Auth::user();
        return $this->party->user->id === $host->id;
    }
}

