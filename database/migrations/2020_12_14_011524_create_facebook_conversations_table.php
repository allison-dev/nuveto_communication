<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('tokenId')->nullable();
            $table->string('sender_id')->nullable();
            $table->string('text')->nullable();
            $table->string('conversationId')->nullable();
            $table->string('farmId')->nullable();
            $table->text('payload')->nullable();
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
        Schema::dropIfExists('facebook_conversations');
    }
}
