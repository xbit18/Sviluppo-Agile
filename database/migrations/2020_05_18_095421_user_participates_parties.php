<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserParticipatesParties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_participates_parties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('party_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('vote')->default(null)->nullable();
            $table->timestamp('timestamp_kick')->nullable();
            $table->timestamp('kick_duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_participates_parties');
        Schema::enableForeignKeyConstraints();
    }
}
