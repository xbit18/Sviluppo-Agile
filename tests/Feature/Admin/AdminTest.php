<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Party;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\User;

class AdminTest extends TestCase
{

    /** @test **/

    public function banned_user_cannot_use_the_app(){
        $user = factory(User::class)->create();
        $user->ban = 1;
        $user->update();

        $response = $this->actingAs($user)->get('/party/show');
        $response->assertRedirect('/ban');
        $response->assertSessionHasErrors(['totalban']);

    }

}
