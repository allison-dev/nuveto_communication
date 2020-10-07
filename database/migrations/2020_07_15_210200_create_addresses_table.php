<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::create('addresses', function (Blueprint $table) {
			$table->id();
			$table->string('street', 255)->nullable();
			$table->string('neighborhood', 150)->nullable();
			$table->string('city')->nullable();
			$table->string('uf', 2)->nullable();
			$table->string('postcode', 8);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('addresses');
	}
}
