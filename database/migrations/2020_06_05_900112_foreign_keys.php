<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('party_has_genre', function(Blueprint $table0) {
            $table0->foreign('party_id')->references('id')
                ->on('parties')->onDelete('cascade'); });

        Schema::table('party_has_genre', function(Blueprint $table0) {
            $table0->foreign('genre_id')->references('id')
                ->on('genres')->onDelete('cascade'); });

        Schema::table('user_participates_parties', function(Blueprint $table0) {
            $table0->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade'); });

        Schema::table('user_participates_parties', function(Blueprint $table0) {
            $table0->foreign('party_id')->references('id')
                ->on('parties')->onDelete('cascade'); });

        Schema::table('parties', function(Blueprint $table0) {
            $table0->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade'); });

        Schema::table('tracks', function(Blueprint $table0) {
            $table0->foreign('party_id')->references('id')
                ->on('parties')->onDelete('cascade'); });
        Schema::table('user_ban_user', function(Blueprint $table0) {
            $table0->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade'); });
        Schema::table('user_ban_user', function(Blueprint $table0) {
            $table0->foreign('ban_user_id')->references('id')
                ->on('users')->onDelete('cascade'); });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
