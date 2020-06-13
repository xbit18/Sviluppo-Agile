<?php

namespace App\Http\Controllers;

use App\Events\SuggestSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Party;
use App\Track;
use App\Events\VoteEvent;
use App\Events\BattleSelectedEvent;
use App\Events\SongAdded;
use App\Events\SongRemoved;
use App\Events\SkipEvent;
use App\Events\AutoSkip;
use App\Events\HostAcceptsDeletesSuggestion;
use App\Events\AutoAddSong;

class TrackController extends Controller
{
    //
    public function getMostVotedSong($code)
    {

        $party = Party::where('code', $code)->first();

        if ($party->type == 'Battle') {
            $res = $party->tracks()->where('active', '!=', 0)->orderByDesc('votes')->first();
            if (empty($res)) return $party->tracks()->first();
        } else
            $res = $party->tracks()->orderByDesc('votes')->first();

        return $res;

    }

    public function resetBattle($code)
    {
        $party = Party::where('code', $code)->first();

        if (Auth::user()->id != $party->user->id) abort(401);

        foreach ($party->users as $user) {
            $party->users()->updateExistingPivot($user->id, ['vote' => null]);
        }

        foreach ($party->tracks as $track) {
            if ($track->active != 0) broadcast(new BattleSelectedEvent(null, $track->active, $party));
            $track->active = 0;
            $track->votes = 0;
            $track->save();
        }

        return $party->tracks;
    }


    public function deleteTrackFromPlaylist($code, $id)
    {
        $party = Party::where('code', $code)->first();
 

        if (Auth::user()->id == $party->user->id) {

            $track = Track::find($id);
            if ($track) $track->delete();

            $votes = $party->users()->where('vote', $id)->get();
            foreach ($votes as $vote) {
                $vote->participates()->updateExistingPivot($party->id, ['vote' => null]);
            }

            foreach ($party->users as $user) {
                $user->participates()->updateExistingPivot($party->id, ['skip' => 0]);
            }

            broadcast(new SongRemoved($party, $track));

            return response()->json('completed');

        } else {
            return response()->json([
                'error' => 'You are not the host of this party'
            ]);
        }

    }

    public function addTrackToPlaylist(Request $request, $code)
    {

        $party = Party::where('code', $code)->first();

        if (Auth::user()->id == $party->user->id) {

            $track = $party->tracks()->create([
                'track_uri' => $request->track_uri
            ]);

            $track_array = collect();
            $track_array->push($track);
            broadcast(new SongAdded($party, $track));

            /* Se la canzone è stata aggiunta dalle canzone suggerite
            dai partecipanti devo mettere a null il campo "suggest_track_uri" di quel partecipante*/

            if($request->user_id){
                /* Prendo il pivot */
                $user = $party->users()->where('user_id', $request->user_id)->first();

                if ($user->pivot->suggest_track_uri === null) {
                    return response()->json([
                        'message' => 'You have not suggested a song yet'
                    ]);
                }

                $user->participates()->updateExistingPivot($party->id,['suggest_track_uri' => null]);
                broadcast(new HostAcceptsDeletesSuggestion($party, $user, true));

                return response()->json([
                    'message' => 'Suggested song removed succesfully'
                ]);
            }

            /* End suggest */

            return response()->json(['id' => $track->id]);

        } else {
            return response()->json([
                'error' => 'You are not the host of this party'
            ]);
        }


    }

    /**
     * Parametri della request:
     * - track_uri
     * - party_code
     */
    public function setTrackActive(Request $request)
    {

        $uri = $request->track_uri;
        $party = Party::where('code', $request->party_code)->first();

        if (Auth::user()->id != $party->user->id) abort(401);
        if ($party->type != 'Battle') abort(400, 'Bad Request : Il party non è di tipo battle');
        if (!$party->tracks()->where('track_uri', $uri)->get()) abort(400, 'Bad Request : Codice della traccia non esistente nel party');
        if ($party->tracks()->where('active', '!=', 0)->count() >= 2) abort(400, 'Bad Request : Track has already 2 active tracks');

        if ($request->side == 1 || $request->side == 2) {
            $side = $request->side;
            if ($party->tracks()->where('active', $side)->count()) abort(400, 'Bad Request: esiste gia un canzone su quel side');

            $mytrack = $party->tracks->where('track_uri', $uri)->first();
            $mytrack->active = $side;
            $mytrack->save();
            broadcast(new BattleSelectedEvent($mytrack, $side, $party));

            return response()->json(['message' => 'Track Activated']);

        }

        abort(400, 'Bad Request');


    }

    /**
     * Parametri della request:
     * - track_uri
     * - party_code
     * - side
     */
    private function setTrackNotActive(Request $request)
    {

        $uri = $request->track_uri;
        $party = Party::where('code', $request->party_code)->first();

        if (Auth::user()->id != $party->user->id) abort(401);
        if ($party->type != 'Battle') abort(400, 'Bad Request : Il party non è di tipo battle');
        if (!$party->tracks()->where('track_uri', $uri)->get()) abort(400, 'Bad Request : Codice della traccia non esistente nel party');

        $mytrack = $party->tracks->where('track_uri', $uri)->first();
        if ($mytrack->active) {
            $mytrack->active = 0;
            $mytrack->save();
            return response()->json(['message' => 'Track Resetted']);
        } else {
            return response()->json(['message' => 'Track is already not active']);
        }

    }

    /*
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi di request : track_id // traccia che si vuole votare
     */
    public function vote($code, $id)
    {

        $user = Auth::user();

        $party = Party::where('code', $code)->first();

        $user_participates = $party->users()->where('user_id', '=', $user->id)
            ->first()->pivot;

        if (!$user_participates) {
            return response()->json([
                'error' => 'You do not participate in this party!'
            ]);
        }


        if ($user_participates->vote === null) {

            $track = Track::find($id);
            if ($track == null) {
                return response()->json([
                    'error' => 'track not found'
                ]);
            }
            $track->votes += 1;
            $track->save();
            broadcast(new VoteEvent($party, $track));
            $user->participates()->updateExistingPivot($party->id, ['vote' => $id]);
            return response()->json([
                'message' => 'track voted successfully'
            ]);
        } else {
            return response()->json([
                'error' => 'You have already voted, remove your vote before voting again',
            ]);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi request : track_id // traccia che si vuole unvotare
     */

    public function unvote($code, $id)
    {

        $user = Auth::user();

        $party = Party::where('code', $code)->first();

        $user_participates = $party->users()->where('user_id', '=', $user->id)
            ->where('party_id', '=', $party->id)->first()->pivot;

        if (!$user_participates) {
            return response()->json([
                'error' => 'You do not participate in this party!'
            ]);
        }


        if ($user_participates->vote !== null && $user_participates->vote == $id) {

            $track = Track::find($id);
            if ($track == null) {
                return response()->json([
                    'error' => 'track not found'
                ]);
            }
            $track->votes -= 1;
            $track->save();
            $user->participates()->updateExistingPivot($party->id, ['vote' => null]);
            broadcast(new VoteEvent($party, $track));
            return response()->json([
                'message' => 'track unvoted successfully',
                'error' => false,
            ]);
        } else {
            return response()->json([
                'error' => 'You have already voted, please remove your vote before voting again',
            ]);
        }
    }

    /*
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi di request : track_id // traccia che si vuole votare
     */
    public function vote_to_skip($code, $id)
    {

        $user = Auth::user();

        $party = Party::where('code', $code)->first();

        $user_participates = $party->users()->where('user_id', '=', $user->id)
            ->first()->pivot;

        if (!$user_participates) {
            return response()->json([
                'error' => 'You do not participate in this party!'
            ]);
        }


        if ($user_participates->skip === 0) {

            $user->participates()->updateExistingPivot($party->id, ['skip' => 1]);
            broadcast(new SkipEvent($party));

            if ($party->users()->where('skip', 1)->count() >= ($party->users()->count() * 0.5)) {
                broadcast(new AutoSkip($party));
            }
            return response()->json([
                'message' => 'track voted to skip successfully'
            ]);


        } else {
            return response()->json([
                'error' => 'You have already voted to skip',
            ]);
        }

    }

    public function suggestSong(Request $request, $code)
    {

        $party = Party::where('code', $code)->first();
        $user_id = Auth::id();
        if (!$party->users->contains('id', $user_id)) {
            return response()->json([
                'message' => 'You do not participate in this party'
            ]);
        }

        /* Prendo lo user */
        $user = $party->users()->where('user_id', $user_id)->first();

        if ($user->pivot->suggest_track_uri !== null) {
            return response()->json([
                'warning' => 'You have already suggested a song,
               please remove your suggestion before suggesting a new song'
            ]);
        }

        $track_uri = $request->track_uri;
        $user->participates()->updateExistingPivot($party->id,['suggest_track_uri' => $track_uri]);
        broadcast(new SuggestSong($party,$track_uri,true));
        return response()->json([
            'message' => 'Song suggested succesfully'
        ]);


    }

    public function removeSuggestedSong(Request $request, $code)
    {
        $party = Party::where('code', $code)->first();
        $user_id = Auth::id();

        /* Se sono l'host */
        if($party->user->id == $user_id){
            $participant = $party->users()->where('user_id', $request->user_id)->first();

            if(!$participant){
                return response()->json([
                    'message' => 'This user does not participate in this party'
                ]);
            }

            $participant->participates()->updateExistingPivot($party->id,['suggest_track_uri' => null]);
            broadcast(new HostAcceptsDeletesSuggestion($party, $participant, false));
            return response()->json([
                'message' => 'Suggested song removed succesfully'
            ]);
        }


        $user = $party->users()->where('user_id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'message' => 'You do not participate in this party'
            ]);
        }

        

        if ($user->pivot->suggest_track_uri === null) {
            return response()->json([
                'message' => 'You have not suggested a song yet'
            ]);
        }

        $user->participates()->updateExistingPivot($party->id,['suggest_track_uri' => null]);

        /* Se sono l'host non faccio partire l'evento */
        if($party->user->id != $user_id){
           broadcast(new SuggestSong($party, null,false)); 
        }
        
        return response()->json([
            'message' => 'Suggested song removed succesfully'
        ]);

    }

    // public function voteSuggestedSong($code, $id){

    //     $party = Party::where('code', $code)->first();

    //     $user = $party->users()->where('user_id', '=', Auth::id())
    //         ->first();

    //     if($user->id == $id){
    //         return response()->json([
    //             'message' => 'You cannot vote your own song'    
    //         ]);
    //     }

    //     $suggested_track_to_vote = $party->users()->where('user_id',$id)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'error' => 'You do not participate in this party!'
    //         ]);
    //     }


    //     if ($user->pivot->suggested_vote === null) {

            
    //         if ($suggested_track_to_vote->pivot->suggest_track_uri == null) {
    //             return response()->json([
    //                 'error' => 'This suggestion does not exist'
    //             ]);
    //         }

    //         $user->participates()->updateExistingPivot($party->id,['suggested_vote' => $id]);
    //         if($party->users()->where('suggested_vote',$id)->count() >= $party->users()->count() * 0.5 ){
    //             $track_uri = $sugged_track_to_vote->pivot->suggest_track_uri;
    //             broadcast(new AutoAddSong($party,$track_uri));
    //         }
    //         return response()->json([
    //             'message' => 'Suggested track voted successfully'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'error' => 'You have already voted a suggested track, remove your vote before voting again',
    //         ]);
    //     }
    // }

    // public function unvoteSuggestedSong($code, $id){

    //     $party = Party::where('code', $code)->first();

    //     $user = $party->users()->where('user_id', '=', Auth::id())
    //         ->first();
            
    //     if($user->id == $id){
    //         return response()->json([
    //             'message' => 'You cannot vote your own song'
    //         ]);
    //     }

    //     $suggested_track_to_vote = $party->users()->where('user_id',$id)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'error' => 'You do not participate in this party!'
    //         ]);
    //     }


    //     if ($user->pivot->suggested_vote !== null) {

            
    //         if ($suggested_track_to_vote->pivot->suggest_track_uri == null) {
    //             return response()->json([
    //                 'error' => 'This suggestion does not exist'
    //             ]);
    //         }

    //         $user->participates()->updateExistingPivot($party->id,['suggested_vote' => $id]);

    //         if($party->users()->where('suggested_vote',$id)->count() >= $party->users()->count() * 0.5 ){
    //             $track_uri = $suggested_track_to_vote->pivot->suggest_track_uri;
    //             $votes = $party->users()->where('suggested_vote',$id)->get();
    //             foreach($votes as $vote){
    //                 $vote->participates()->updateExistingPivot($party->id,['suggested_vote' => null]);
    //             }
    //             broadcast(new AutoAddSong($party,$track_uri));
    //         }
    //         return response()->json([
    //             'message' => 'Suggested track unvoted successfully'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'error' => 'An error has occurred, please try again',
    //         ]);
    //     }
    // }

}
