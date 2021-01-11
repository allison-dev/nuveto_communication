<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('facebook_page_id')->nullable();
            $table->string('whatsapp_phone')->nullable();
            $table->string('clientId')->nullable();
            $table->string('secretId')->nullable();
            $table->text('refreshToken')->nullable();
            $table->string('callbackUrl')->nullable();
            $table->string('campaignName')->nullable();
            $table->string('tenantName')->nullable();
            $table->string('channel')->dafault('chat');
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
        Schema::dropIfExists('setting');
    }
}
