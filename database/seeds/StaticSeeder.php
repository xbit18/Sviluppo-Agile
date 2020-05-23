<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StaticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('genres')->insert(['genre' => 'Blues']);
        DB::table('genres')->insert(['genre' => 'R&B and Soul']);
        DB::table('genres')->insert(['genre' => 'Jazz']);
        DB::table('genres')->insert(['genre' => 'Rock']);
        DB::table('genres')->insert(['genre' => 'Metal']);
        DB::table('genres')->insert(['genre' => 'Pop']);
        DB::table('genres')->insert(['genre' => 'Punk']);
        DB::table('genres')->insert(['genre' => 'Electronic']);
        DB::table('genres')->insert(['genre' => 'House']);
        DB::table('genres')->insert(['genre' => 'Latin']);
        DB::table('genres')->insert(['genre' => 'Rap / Hip Hop']);
        DB::table('genres')->insert(['genre' => 'Alternative']);
        DB::table('genres')->insert(['genre' => 'Other']);


        /**
         * ADDING STATIC USER
         */
        $user = array(
            'name' => 'Statico',
            'email' => 'static@e.it',
            'password' => '$2y$10$xlQJOc3MoU9Fpz/OWpU8QeXSlntx16/Mddfb7p19/CmDUD0NKnWX2', // password : passpass
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        );

        DB::table('users')->insert($user);

    }
}
