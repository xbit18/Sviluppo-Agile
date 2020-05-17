<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class logInTest extends TestCase
{
    
    use RefreshDatabase;

    /** @test **/
    public function if_not_logged_in_redirect_log_in_page()
    {
        $response = $this->get('/home');

        $response->assertRedirect('/login');
    }

    /** @test **/

    public function users_can_register(){

        $this->withoutExceptionHandling();
        $response = $this->get('register');
        $response->assertStatus(200);

        $response = $this->post('/register', $this->data());

        $this->assertCount(1, User::all());
        $response->assertRedirect('/email/verify')->assertStatus(302);
        
    }

    /** @test **/

    public function users_can_log_in(){
         
        $this->withoutExceptionHandling();

        $response = $this->get('/login');
        $response->assertStatus(200);

        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'password123'),
        ]);
    
        $response = $this->post('/login',[
            'email' => $user->email,
            'password' => $password,
        ]);
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);

    }

    /** @test **/

    public function users_cannot_log_in_with_incorrect_password(){

        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'password123')
        ]);

        $response = $this->from('/login')->post('/login',[
            'email' => $user->email,
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
