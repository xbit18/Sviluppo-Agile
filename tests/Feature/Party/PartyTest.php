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
        $user = factory(User::class)->create();
        $user->markEmailAsVerified();
        /*$genre = Genre::create([
            'genre' => 'rock'
        ]);*/

        $response = $this->actingAs($user)->post('/party',$this->data());

        /**
         * La frase Party created succesfully è stata tolta
         * Controllo che stia redirezionando verso il link giusto
         */
        $response->assertSee('Redirecting to http://localhost/me/party/show');
    }

    /** @test **/

    public function party_link_with_code_works(){
        $code = Str::random(16);
        $party = Party::create([
            'user_id' => 1,
            'name' => 'Prova Nome',
            'mood' => 'Prova Mood',
            'type' => 'Democracy',
            'source' => 'Youtube',
            'description' => 'Prova Description',
            'code' => $code
        ]);

        $party->genre()->attach(4);

        $user = factory(User::class)->create();
        $user->markEmailAsVerified();
        $response = $this->actingAs($user)->get('/party/show/'.$code);

        $response->assertJson([
            'id' => $party->id,
        ]);
    }

    /** @test **/

    public function party_link_with_fake_code_fails(){
        do{
            $code = Str::random(16);
        }
        while(Party::where('code', '=', $code)->exists());


        $user = factory(User::class)->create();
        $user->markEmailAsVerified();
        $response = $this->actingAs($user)->get('/party/show/'.$code);

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
