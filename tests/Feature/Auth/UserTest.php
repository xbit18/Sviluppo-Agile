<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class UserTest extends TestCase
{
    
    use RefreshDatabase;
    protected $user;
    protected $password;

    public function setUp() :void{

        parent::setUp();

        $this->user = factory(User::class)->create([
            'password' => bcrypt($this->password = 'password123'),
        ]);
        $this->user->markEmailAsVerified();

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
        $this->assertCount(3, User::all());
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

    private function data(){
        return [
            'name' => 'Bryant Sarabia',
            'email' => 'bryantsarabia@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
    }

}
