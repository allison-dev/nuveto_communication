<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->nullable();
            $table->integer('address_id')->nullable();
            $table->string('number_home')->nullable();
            $table->string('complement')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('cellphone')->nullable();
            $table->string('cpf_cnpj')->nullable();
            $table->string('birthday')->nullable();
            $table->string('street')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('uf')->nullable();
            $table->string('postcode')->nullable();
            $table->string('date_ini')->nullable();
            $table->string('date_end')->nullable();
            $table->string('total')->nullable();
            $table->string('subtotal')->nullable();
            $table->string('full_total')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
