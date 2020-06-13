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

    
}
