<?php

namespace Tests\Feature\Party;

use App\Party;
use Carbon\Carbon;
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
    protected $host;
    protected $participant;
    protected $code;

    public function setUp() :void{

        parent::setUp();

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
        $this->assertCount(1, Party::all());


    }

     /** @test **/

     public function party_participant_cannot_update_party(){


        $response = $this->actingAs($this->participant)->post('/party/update/'.$this->party->code,[
            'mood' => 'Mood aggiornato',
            'type' => 'Democracy',
            'desc' => "Descrizione aggiornata",
            'genre' => array(67), //id associato al genere Jazz
        ]);

         $response->assertSessionHasErrors(['error']);
    }


    /** @test **/
    public function party_host_can_update_party()
    {

        /**
         * Creo un party
         */
        $code = Str::random(16);

        $party = Party::create([
            'user_id' => $this->host->id,
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

        $response = $this->actingAs($this->host)->post('/party/update/'.$code,[
            'mood' => 'Mood aggiornato',
            'type' => 'Democracy',
            'desc' => "Descrizione aggiornata",
            'genre' => array(67), //id associato al genere Jazz
        ]);

        $response->assertRedirect('/party/show/'.$code);

        /**
         * Controllo che i campi del party siano stati aggiornati e restituiti alla view con successo
         */

         $response = $this->ActingAs($this->host)->get('/party/show/'.$code);

        $response->assertSee('Mood aggiornato')
                 ->assertSee('Democracy')
                 ->assertSee('Descrizione aggiornata')
                 ->assertSee('Jazz');
    }


    /** @test **/

    public function party_link_with_code_works(){

        $response = $this->actingAs($this->host)->get('/party/show/'.$this->party->code);

        $response->assertStatus(200);
    }

    /** @test **/

    public function party_link_with_fake_code_fails(){
        do{
            $this->code = Str::random(16);
        }
        while(Party::where('code', '=', $this->code)->exists());

        $response = $this->actingAs($this->host)->get('/party/show/'.$this->code);

        $response->assertSessionHasErrors(['message']);
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
        $this->actingAs($this->host)->get('/party/show/'.$this->party->code);

        // ENTRA IL PARTECIPANTE
        $this->actingAs($user_part)->get('/party/show/'.$this->party->code);

        $response = $this->actingAs($this->host)->get('/party/' . $this->party->code . '/join/' . $user_part->id);

        $response->assertSee($this->party->code);

    }

    /** @test */
    public function user_can_leave() {

        $user_part = factory(User::class)->create();
        $user_part->name = 'Participant';
        $user_part->email = 'participant@example.com';
        $user_part->markEmailAsVerified();

        // ENTRA L'HOST
        $this->actingAs($this->host)->get('/party/show/'.$this->party->code);

        // ENTRA IL PARTECIPANTE
        $this->actingAs($user_part)->get('/party/show/'.$this->party->code);

        $this->actingAs($this->host)->get('/party/' . $this->party->code . '/join/' . $user_part->id);
        $response = $this->actingAs($this->host)->get('/party/' . $this->party->code . '/leave/' . $user_part->id);

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


       /** @test */
       public function participant_cannot_add_song() {

        $song_data = [
            'track_uri' => 'spotify:track:132qd23f4f'
        ];
        $response = $this->actingAs($this->participant)->post('/party/'.$this->code.'/tracks/', $song_data);

        $response->assertExactJson([
            'error' => 'You are not the host of this party'
        ]);

    }


    /** @test */
    public function host_can_add_song() {

        $song_data = [
            'track_uri' => 'spotify:track:132qd23f4f'
        ];
        $response = $this->actingAs($this->host)->post('/party/'.$this->code.'/tracks/', $song_data);

        $this->assertTrue($this->party->tracks()->where('track_uri', 'spotify:track:132qd23f4f')->count() == 1);

    }


    /** @test **/

    public function participants_cannot_remove_song(){

        $this->actingAs($this->host)->post('/party/'.$this->code.'/tracks/',  ['track_uri' => 'spotify:track:132qd23f4f']);

        $song_id = $this->party->tracks()->where('track_uri', 'spotify:track:132qd23f4f')->pluck('id')->first();

        $response = $this->actingAs($this->participant)->delete('/party/'.$this->code.'/tracks/'.$song_id);
        $response->assertExactJson([
            'error' => 'You are not the host of this party'
            ]);

    }

    /** @test */
    public function host_can_remove_song() {

        $this->actingAs($this->host)->post('/party/'.$this->code.'/tracks/',  ['track_uri' => 'spotify:track:132qd23f4f']);
        $song_id = $this->party->tracks()->where('track_uri', 'spotify:track:132qd23f4f')->pluck('id')->first();

        $this->actingAs($this->host)->delete('/party/'.$this->code.'/tracks/'.$song_id);
        $this->assertTrue($this->party->tracks()->where('track_uri', 'spotify:track:132qd23f4f')->count() == 0);

    }

    /** @test * */

    public function participant_cannot_kick_a_user()
    {

        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $this->actingAs($this->participant)->get('/party/show/' . $this->party->code);
        $this->actingAs($user)->get('/party/show/' . $this->party->code);

        $response = $this->actingAs($user)->post('/party/' . $this->party->code . '/user/' . $this->participant->id . '/kick', [
            'kick_duration' => Carbon::now('Europe/London')
        ]);
        $response->assertExactJson([
            'message' => 'You are not the host of this party',
            'error' => true,
        ]);


    }

    /** @test * */

    public function host_cannot_kick_a_non_existent_user()
    {

        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $user->delete();


        $response = $this->actingAs($this->participant)->post('/party/' . $this->party->code . '/user/' . $user->id . '/kick', [
            'kick_duration' => Carbon::now()
        ]);
        $response->assertExactJson([
            'message' => 'This user does not exist',
            'error' => true,
        ]);
    }


    /** @test * */

    public function host_cannot_kick_a_user_that_does_not_participate_in_that_party()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($this->host)->post('/party/' . $this->party->code . '/user/' . $user->id . '/kick', [
            'kick_duration' => Carbon::now()
        ]);
        $response->assertExactJson([
            'message' => 'The given user does not participate in this party',
            'error' => true,
        ]);


    }

    /** @test **/

    public function host_cannot_set_kick_duration_earlier_than_now(){

        $user = factory(User::class)->create();
        $this->actingAs($user)->get('/party/show/' . $this->party->code);
        $response = $this->actingAs($this->host)->post('/party/' . $this->party->code . '/user/' . $user->id . '/kick', [
            'kick_duration' => '2020-02-24 00:00:00'
        ]);
        $response->assertExactJson([
            'message'=> 'kick time cannot be earlier than now',
            'error' => true,
        ]);

    }

    /** @test **/

    public function host_can_kick_a_participant(){

        $user = factory(User::class)->create();
        $this->actingAs($user)->get('/party/show/' . $this->party->code);
        $response = $this->actingAs($this->host)->post('/party/' . $this->party->code . '/user/' . $user->id . '/kick', [
            'kick_duration' => Carbon::now('Europe/London')->addHours(2)
        ]);
        $response->assertExactJson([
            'message' => 'The user has been kicked succesfully',
            'error' => false,
        ]);

    }

    /** @test **/

    public function kicked_participant_cannot_enter_to_the_party(){

        $user = factory(User::class)->create();
        $this->actingAs($user)->get('/party/show/' . $this->party->code);
        $this->actingAs($this->host)->post('/party/' . $this->party->code . '/user/' . $user->id . '/kick', [
            'kick_duration' => Carbon::now('Europe/London')->addHours(2)
        ]);

        $response = $this->ActingAs($user)->get('/party/show/'.$this->party->code);
        $response->assertSessionHasErrors('error');

    }


    /** @test **/

    public function participants_cannot_delete_party(){

        $response = $this->actingAs($this->participant)->delete('/party/'.$this->party->id.'/delete');
        $response->assertExactJson([
            'error' => 'This party is not yours'
        ]);

    }

    /** @test **/

    public function party_owner_can_delete_party(){

        $response = $this->actingAs($this->host)->delete('/party/'.$this->party->id.'/delete');

        $response->assertExactJson([
            'message' => 'Party deleted'
            ]);
        $response->assertSessionHasNoErrors();
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
