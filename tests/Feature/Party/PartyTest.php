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
        $genre = Genre::create([
            'genre' => 'Rock'
        ]);
        
        $response = $this->actingAs($user)->post('/party',$this->data());

        $response->assertSee('Party created succesfully');
    }

    private function data(){
        return [
            'name' => 'ProvaNome',
            'genre' => 'Rock',
            'mood' => 'ProvaMood',
            'type' => 'Battle',
            'source' => 'Youtube'
        ];
    }
}
