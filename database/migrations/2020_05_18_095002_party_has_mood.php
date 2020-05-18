<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PartyHasMood extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('party_has_mood', function (Blueprint $table) {
           $table->id();
           $table->unsignedBigInteger('party_id');
           $table->unsignedBigInteger('mood_id');
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
        Schema::dropIfExists('party_has_mood');
        Schema::enableForeignKeyConstraints();
    }
}
