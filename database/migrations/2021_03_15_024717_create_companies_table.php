<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('address_id')->unsigned()->nullable();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null')->onUpdate('set null');
            $table->string('number_home')->nullable();
            $table->string('complement')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('cellphone', 12)->nullable();
            $table->string('cpf_cnpj', 14);
            $table->date('birthday')->nullable();
            $table->enum('sex', [
                'F',
                'M',
            ])->default('F');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company');
    }
}
