<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversation_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('tokenId')->nullable();
            $table->bigInteger('userId')->unsigned()->nullable();
            $table->foreign('userId')->references('id')->on('users')->onDelete('set null')->onUpdate('set null');
            $table->string('conversationId')->nullable();
            $table->string('tenantId')->nullable();
            $table->string('farmId')->nullable();
            $table->tinyInteger('terminate')->nullable()->default(0);
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
        Schema::dropIfExists('conversation_sessions');
    }
}
