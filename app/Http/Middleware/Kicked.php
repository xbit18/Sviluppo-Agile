<?php

namespace App\Http\Middleware;
use App\Party;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Closure;

class Kicked
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

        $kick_duration = $party->users()->where('user_id',$user->id)->first();
        
        if($kick_duration){
            if( $kick_duration->pivot->kick_duration > Carbon::now()){
            return redirect('/party/show')->with(['kicked',true]);
        }
        }

        return $next($request);
    }
}
