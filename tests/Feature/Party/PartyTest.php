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
            'source' => 'Spotify',
            'description' => 'Prova Description',
            'playlist_id' => null,
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
        $response->assertSee('Redirecting to');
    }

    /** @test **/
    public function party_is_updated()
    {
        /**
         * Creo un party
         */
        $code = Str::random(16);

        $party = Party::create([
            'user_id' => $this->user->id,
            'name' => 'Prova Nome',
            'mood' => 'Prova Mood',
            'type' => 'Democracy',
            'source' => 'Spotify',
            'description' => 'Prova Description',
            'code' => $code
        ]);
        $this->party->genre()->attach(4);

        /**
         * Modifico il party con dei parametri specifici
         */
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->user)->post('/party/update/'.$code,[
            'mood' => 'Mood aggiornato',
            'type' => 'Democracy',
            'desc' => "Descrizione aggiornata",
            'genre' => array(67), //id associato al genere Jazz
        ]);

        $response = $this->actingAs($this->user)->get('/party/show/'.$code);

        /**
         * Controllo che i campi del party siano stati aggiornati e restituiti alla view con successo
         */

        $response->assertSee('Mood aggiornato')
                 ->assertSee('Democracy')
                 ->assertSee('Descrizione aggiornata')
                 ->assertSee('<li><a href="#">Jazz</a></li>',false);
    }

    /** @test **/

    public function party_link_with_code_works(){

        $response = $this->actingAs($this->user)->get('/party/show/'.$this->party->code);

        $response->assertStatus(200);
    }

    /** @test **/

    public function party_link_with_fake_code_fails(){
        do{
            $this->code = Str::random(16);
        }
        while(Party::where('code', '=', $this->code)->exists());

        $response = $this->actingAs($this->user)->get('/party/show/'.$this->code);

        $response->assertStatus(500);
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

        // Redirezionamento verso back
        $response->assertStatus(302);

    }

    /** @test */
    public function user_can_join() {

        
        $user_part = factory(User::class)->create();
        $user_part->name = 'Participant';
        $user_part->email = 'participant@example.com';
        $user_part->markEmailAsVerified();

        // ENTRA L'HOST
        $this->actingAs($this->user)->get('/party/show/'.$this->party->code);

        // ENTRA IL PARTECIPANTE
        $this->actingAs($user_part)->get('/party/show/'.$this->party->code);
        
        $response = $this->actingAs($this->user)->get('/party/' . $this->party->code . '/join/' . $user_part->id);

        $response->assertSee($this->party->code);

    }

    /** @test */
    public function user_can_leave() {

        $user_part = factory(User::class)->create();
        $user_part->name = 'Participant';
        $user_part->email = 'participant@example.com';
        $user_part->markEmailAsVerified();

        // ENTRA L'HOST
        $this->actingAs($this->user)->get('/party/show/'.$this->party->code);

        // ENTRA IL PARTECIPANTE
        $this->actingAs($user_part)->get('/party/show/'.$this->party->code);
        
        $this->actingAs($this->user)->get('/party/' . $this->party->code . '/join/' . $user_part->id);
        $response = $this->actingAs($this->user)->get('/party/' . $this->party->code . '/leave/' . $user_part->id);

        $response->assertDontSee($this->party->code);

    }

    /** @test */
    /*
     * Non possiamo eseguire più questo test perche non abbiamo errori in questo caso
     * ma semplicemente non viene inviata una email
     *
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
    */

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
            'source' => 'Spotify',
            'desc' => "description"
        ];
    }
}
