<?php

namespace Tests\Feature\Party;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class PartyTest extends TestCase
{
    use RefreshDatabase;
     /** @test **/
    public function party_is_created()
    {

        $this->withoutMiddleware();
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'email_verified_at' => date("Y-m-d H:i:s")
        ]);
        
        $response = $this->actingAs($user)->post('/party',$this->data());

        $response->assertStatus(302)->assertRedirect('/party');
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
