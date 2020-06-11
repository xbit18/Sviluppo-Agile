<?php

namespace Tests\Feature;

use App\Party;
use App\UserBanUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\User;

class UserTest extends TestCase
{

    use RefreshDatabase;
    protected $user;
    protected $password;
    protected $party;
    protected $host;
    protected $participant;
    protected $code;

    public function setUp() :void{

        parent::setUp();

        $this->user = factory(User::class)->create([
            'password' => bcrypt($this->password = 'password123'),
        ]);
        $this->user->markEmailAsVerified();

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
    public function if_not_logged_in_redirect_log_in_page()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /** @test **/

    public function users_can_register(){

        $this->withoutExceptionHandling();
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', $this->data());

        /**
         * Modificato 2 in 3 visto che esiste l'utente statico, un altro viene creato al testare la registrazione e l'altro al iniziare il test con la funzione setUp()
         */
        $this->assertCount(5, User::all());
        $response->assertRedirect('/email/verify')->assertStatus(302);

    }

    /** @test **/

    public function users_can_log_in(){

        $this->withoutExceptionHandling();

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login',[
            'email' => $this->user->email,
            'password' => $this->password
        ]);
        $response->assertRedirect('/loginspotify');
        $this->assertAuthenticatedAs($this->user);

    }

    /** @test **/

    public function users_cannot_log_in_with_incorrect_password(){


        $response = $this->from('/login')->post('/login',[
            'email' => $this->user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test **/

    public function user_gets_banned(){

        $this->actingAs($this->host)->get('/party/'.$this->party->code.'/user/'.$this->participant->id.'/ban');
        $response = UserBanUser::where('user_id','=',$this->host->id)->where('ban_user_id','=',$this->participant->id)->get();
        $this->assertCount(1,$response);

    }

    /** @test **/

    public function banned_user_cannot_access_party(){
        $response = $this->actingAs($this->participant)->get('/party/show'.$this->party->code);
        $response->assertSessionHasErrors(['error']);
    }


    private function data(){
        return [
            'name' => 'Bryant Sarabia',
            'email' => 'bryantsarabia@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
    }

}
