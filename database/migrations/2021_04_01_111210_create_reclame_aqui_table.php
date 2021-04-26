<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReclameAquiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reclame_aqui', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->nullable();
            $table->boolean('reply')->default(0)->nullable();
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->json('customer_phones')->nullable();
            $table->string('complaint_title')->nullable();
            $table->text('complaint_content')->nullable();
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
        Schema::dropIfExists('reclame_aqui');
    }
}
