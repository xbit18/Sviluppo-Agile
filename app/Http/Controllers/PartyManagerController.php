<?php

namespace App\Http\Controllers;
use App\UserBanUser;
use App\Track;
use App\User;
use App\Party;
use App\UserParticipatesParty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\KickEvent;
use App\Events\BanEvent;

class PartyManagerController extends Controller
{


    /**
     * @param Request $request
     * campi request :
     * user // user che si vuole cacciare
     * time // tempo di kick
     */
   public function kick(Request $request, $code, $user_id){

       if(Auth::user()->id == $user_id)
       {
           return response()->json([
               'message' => 'You cannot kick yourself',
               'error' => true,
           ]);
       }


       $party = Party::where('code',$code)->first();
   
       $user = $party->users()->where('user_id',$user_id)->first();
       if($user == null) {
           return response()->json([
               'message' => 'The given user does not participate in this party',
               'error' => true,
           ]);

       }
       $user->participates()->updateExistingPivot($party->id,['timestamp_kick' => Carbon::now('Europe/London')] );

       
       if(($request->kick_duration) < (Carbon::now('Europe/London'))){
           return response()->json()([
               'message'=> 'kick time cannot be earlier than now',
               'error' => true,
               ]);
       }

       $user->participates()->updateExistingPivot($party->id,['kick_duration' => $request->kick_duration] );

       broadcast(new KickEvent($party,$user));


       return response()->json([
           'message' => 'The user has been kicked succesfully',
           'error' => false,
       ]);

   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi request :
     * user // user che si vuole rimettere
     */


    public function unkick(Request $request){
        if(Auth::user()->id == $request->user)
        {
            return redirect()->back()->withErrors(['you cannot unkick yourself']);
        }
        $party=UserParticipatesParty::where('user_id',$request->user)->first(); // user che voglio kickare
        if($party == null) {
            return redirect()->back()->withErrors(['User not partecipate in any parties']);
        }
        $party->timestamp_kick=null;
        $party->kick_duration=null;
        $party->save();
        return redirect()->back()->with('success', 'The user kicked successfully');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi request:
     *  user // id user che si vuole bannare
     */


   public function ban($code, $user_id){


        $user=Auth::user();
        $user_to_ban = User::find($user_id);
       if($user_to_ban === null)
       {
           return response()->json([
               'message' => 'This user does not exists',
               'error' => true,
           ]);
       }

       if($user->id == $user_to_ban->id)
       {
           return response()->json([
               'message' => 'You cannot ban yourself' ,
               'error' => true,
           ]);
       }
       
       $already_banned = $user->bans()->where('ban_user_id', $user_to_ban->id)->first();

       if($already_banned == null){

        $party = Party::where('code', $code)->first();
        $user->bans()->syncWithoutDetaching($user_to_ban->id);
        broadcast(new BanEvent($party,$user_to_ban));
           return response()->json([
               'message' => 'User '. $user_to_ban->name.' '.'banned succesfully!',
               'error' => false,
           ]);
       }
       else{
           return response()->json([
               'message' => 'User already banned',
               'error' => true,
           ]);
       }
   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi request:
     *  user // id user che si vuole unbannare
     */


    public function unban($code, $user_id){

        $user=Auth::user();
        $user_to_ban = User::find($user_id);
       if($user_to_ban === null)
       {
           return response()->json([
               'message' => 'This user does not exists',
               'error' => true,
           ]);
       }

       if($user->id == $user_to_ban->id)
       {
           return response()->json([
               'message' => 'You cannot unban yourself' ,
               'error' => true,
           ]);
       }
       
       $already_banned = $user->bans()->where('ban_user_id', $user_to_ban->id)->first();

       if($already_banned !== null){

        $party = Party::where('code', $code)->first();
        $user->bans()->detach($user_to_ban->id);
           return response()->json([
               'message' => 'User '. $user_to_ban->name.' '.'unbanned succesfully!',
               'error' => false,
           ]);
       }
       else{
           return response()->json([
               'message' => 'User already banned',
               'error' => true,
           ]);
       }
    }
}
