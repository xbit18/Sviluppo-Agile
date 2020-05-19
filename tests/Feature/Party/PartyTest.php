<?php

namespace Tests\Feature\Party;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
