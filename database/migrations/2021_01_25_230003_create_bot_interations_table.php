<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotInterationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_interations', function (Blueprint $table) {
            $table->id();
            $table->text('text')->nullable();
            $table->text('order')->nullable();
            $table->text('options')->nullable();
            $table->text('response')->nullable();
            $table->tinyInteger('send_five9')->nullable()->default(0);
            $table->tinyInteger('terminate')->nullable()->default(0);
            $table->text('bot_variable')->nullable();
            $table->text('bot_choice')->nullable();
            $table->text('sender_email')->nullable();
            $table->text('sender_id')->nullable();
            $table->integer('bot_order')->nullable();
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
        Schema::dropIfExists('bot_interations');
    }
}
