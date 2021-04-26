<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReclameAquiConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reclame_aqui_conversation', function (Blueprint $table) {
            $table->id();
            $table->string('tokenId')->nullable();
            $table->string('ticket_id')->nullable();
            $table->text('text')->nullable();
            $table->string('conversationId')->nullable();
            $table->string('farmId')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reclame_aqui_conversation');
    }
}
