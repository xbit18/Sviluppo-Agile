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

        $response->assertStatus(200);
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

    /** @test */
    public function user_can_invite_people() {
        
        $user = factory(User::class)->create();
        $user->name = 'Bryant';
        $user->email = 'bryantsarabia@example.com';
        $user->markEmailAsVerified();

        $data_invite = [
            'invite_list' => '(' . $user->name . ' - ' . $user->email . ')',
        ];

        $response = $this->actingAs($user)->post('/party/ederWGcVCp0ASTqx/invite', $data_invite);

        $response->assertJson([ 
            [
                "name" => "Bryant",
                "email" => "bryantsarabia@example.com"
            ]
         ]);

    }

    /** @test */
    public function user_cannot_invite_fake_people() {
        
        $user = factory(User::class)->create();
        $user->name = 'Bryant';
        $user->email = 'bryantsarabia@example.com';
        $user->markEmailAsVerified();

        $data_invite = [
            'invite_list' => '(' . $user->name . 'de - de' . $user->email . ')',
        ];

        $response = $this->actingAs($user)->post('/party/ederWGcVCp0ASTqx/invite', $data_invite);

        $response->assertJson([]);

    }

    /** @test */
    public function user_cannot_manipulate_invite_field() {
        
        $user = factory(User::class)->create();
        $user->name = 'Bryant';
        $user->email = 'bryantsarabia@example.com';
        $user->markEmailAsVerified();

        //INVALIDO L'EMAIL 
        $data_invite = [
            'invite_list' => '(' . $user->name . 'de - ' . $user->email . 'cde)',
        ];

        $response = $this->actingAs($user)->post('/party/ederWGcVCp0ASTqx/invite', $data_invite);

        // Ritorna 400 -> Bad Request
        $response->assertStatus(400);

    }

    /** @test */
    public function user_can_find_name_by_email_from_ajax() {

        $user = factory(User::class)->create();
        $user->name = 'Bryant';
        $user->email = 'bryantsarabia@example.com';
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get('/users/' . $user->email . '/nome', array('HTTP_X-Requested-With' => 'XMLHttpRequest'));

        // Ritorna 400 -> Bad Request
        $response->assertSee('Bryant');
    }

    /** @test */
    public function user_cannot_find_name_by_email_if_not_from_ajax() {

        $user = factory(User::class)->create();
        $user->name = 'Bryant';
        $user->email = 'bryantsarabia@example.com';
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get('/users/' . $user->email . '/nome');

        // Ritorna 400 -> Bad Request
        $response->assertStatus(500);
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
            'code' => 'ederWGcVCp0ASTqx',
            'source' => 'Youtube',
            'desc' => "description"
        ];
    }
}
