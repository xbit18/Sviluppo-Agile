<?php

namespace Tests\Feature\Party;

use App\Party;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\User;
use App\Genre;

class PartyTest extends TestCase
{
    use RefreshDatabase;
    protected $party;
    protected $user;
    protected $code;

    public function setUp() :void{

        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user->markEmailAsVerified();

        $this->code = Str::random(16);

        $this->party = Party::create([
            'user_id' => $this->user->id,
            'name' => 'Prova Nome',
            'mood' => 'Prova Mood',
            'type' => 'Democracy',
            'source' => 'Youtube',
            'description' => 'Prova Description',
            'code' => $this->code
        ]);
        $this->party->genre()->attach(4);
    }

    /** @test **/

    public function only_if_logged_in_can_create_party(){

        $response = $this->get('/party/create');
        $response->assertRedirect('/login');

    }

    /** @test **/

    public function if_not_logged_in_cannot_create_party(){

        $response = $this->post('/party',$this->data());
        $response->assertRedirect('/login');

    }


     /** @test **/
    public function party_is_created()
    {

        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->user)->post('/party',$this->data());

        /**
         * La frase Party created succesfully è stata tolta
         * Controllo che stia redirezionando verso il link giusto
         */
        $response->assertSee('Redirecting to http://localhost/me/party/show');
    }

    /** @test **/

    public function party_link_with_code_works(){

        $response = $this->actingAs($this->user)->get('/party/show/'.$this->party->code);

        $response->assertJson([
            'id' => $this->party->id,
        ]);
    }

    /** @test **/

    public function party_link_with_fake_code_fails(){
        do{
            $this->code = Str::random(16);
        }
        while(Party::where('code', '=', $this->code)->exists());

        $response = $this->actingAs($this->user)->get('/party/show/'.$this->code);

        $response->assertJson([
            'error' => 'This party does not exist',
        ]);
    }

    /**
     * Aggiungendo la validazione i campi devono essere così
     * $validatedData = $request->validate([
     *      'name' => 'required|string',
     *      'mood' => 'required|string',
     *      'type' => 'required|in:Battle,Democracy',
     *      'desc' => 'required|string',
     *      'genre' =>'required|array'
     *   ]);
     */
    private function data(){
        return [
            'name' => 'ProvaNome',
            'genre' => array('Rock'),
            'mood' => 'ProvaMood',
            'type' => 'Battle',
            'source' => 'Youtube',
            'desc' => "description"
        ];
    }
}
