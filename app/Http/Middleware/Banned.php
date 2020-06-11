<?php

namespace App\Http\Middleware;
use App\Party;
use Illuminate\Support\Facades\Auth;
use Closure;

class Banned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        $code = $request->route('code');
        $party = Party::where('code','=',$code)->first();
        $user = Auth::user();
        if(!$party){
            return redirect()->route('parties.index')->withErrors([
                'message' => 'This party does not exist'
            ]);
        }
        $party_owner = $party->user;

        if($party_owner->bans()->where('ban_user_id',$user->id)->first() != null){
            return redirect()->route('parties.index')->withErrors([
                'error' => 'This host has banned you'
            ]);
        }


        return $next($request);
    }
}
