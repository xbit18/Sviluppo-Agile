<?php

namespace Tests\Feature\Party;

use App\Party;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\User;

class SuggestionsTest extends TestCase
{
    use RefreshDatabase;
    protected $party;
    protected $host;
    protected $participant;
    protected $code;

    public function setUp() :void{

        parent::setUp();

        $this->host = factory(User::class)->create();
        $this->host->markEmailAsVerified();

        $this->participant = factory(User::class)->create();
        $this->participant->markEmailAsVerified();

        $this->code = Str::random(16);

        $this->party = Party::create([
            'user_id' => $this->host->id,
            'name' => 'Prova Nome',
            'mood' => 'Prova Mood',
            'type' => 'Democracy',
            'source' => 'Spotify',
            'description' => 'Prova Description',
            'playlist_id' => null,
            'code' => $this->code
        ]);
        $this->party->genre()->attach(4);
    }

    /** @test **/

    public function if_not_participate_cannot_suggest_a_song(){

        $response = $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/add',[
            'track_uri' => 'spotify:track:2u02P0y6omgiLmEVCd7WdV'
        ]);

        $response->assertExactJson([
            'message' => 'You do not participate in this party'
        ]);

    }

    /** @test **/
    public function participant_can_suggest_a_song(){

        $this->actingAs($this->participant)->get('/party/show/'.$this->party->code);
        $response =  $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/add',[
            'track_uri' => 'spotify:track:2u02P0y6omgiLmEVCd7WdV'
        ]);
        $response->assertExactJson([
            'message' => 'Song suggested succesfully'
        ]);
    }

    /** @test **/

    public function participant_cannot_suggest_a_song_if_already_suggested(){

        $this->actingAs($this->participant)->get('/party/show/'.$this->party->code);

        $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/add',[
            'track_uri' => 'spotify:track:2u02P0y6omgiLmEVCd7WdV'
        ]);

        $response =  $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/add',[
            'track_uri' => 'spotify:track:2u02P0y6omgiLmEVCd7WdV'
        ]);

        $response->assertExactJson([
            'warning' => 'You have already suggested a song,
               please remove your suggestion before suggesting a new song'
        ]);
    }


    /** @test **/

    public function host_cannot_remove_a_suggested_song_of_a_non_participant_user(){

        $response =  $this->actingAs($this->host)->post('/party/'.$this->party->code.'/tracks/suggest/remove',[
            'user_id' => $this->participant->id,
        ]);
        $response->assertExactJson([
                'message' => 'This user does not participate in this party'
            ]);
        
    }

    /** @test **/

    public function host_can_remove_a_suggested_song(){

        $this->actingAs($this->participant)->get('/party/show/'.$this->party->code);
        $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/add',[
            'track_uri' => 'spotify:track:2u02P0y6omgiLmEVCd7WdV'
        ]);
        
        $response =  $this->actingAs($this->host)->post('/party/'.$this->party->code.'/tracks/suggest/remove',[
            'user_id' => $this->participant->id,
        ]);

        $response->assertExactJson([
            'message' => 'Suggested song removed succesfully'
        ]);

    }

    /** @test **/

    public function if_not_participant_cannot_remove_a_suggested_song(){

        $response = $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/remove',[]);

        $response->assertExactJson([
            'message' => 'You do not participate in this party'
        ]);

    }

     /** @test **/

     public function participant_can_remove_a_suggested_song(){

        $this->actingAs($this->participant)->get('/party/show/'.$this->party->code);
        $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/add',[
            'track_uri' => 'spotify:track:2u02P0y6omgiLmEVCd7WdV'
        ]);
        $response = $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/remove',[]);
        $response->assertExactJson([
            'message' => 'Suggested song removed succesfully'
        ]);
    }

    /** @test **/

    public function if_participant_has_not_suggested_a_song_cannot_remove_a_suggested_song(){

        $this->actingAs($this->participant)->get('/party/show/'.$this->party->code);
        $response = $this->actingAs($this->participant)->post('/party/'.$this->party->code.'/tracks/suggest/remove',[]);
        $response->assertExactJson([
            'message' => 'You have not suggested a song yet'
        ]);
    }

   
}
