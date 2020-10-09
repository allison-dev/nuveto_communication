<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::create('doctors', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('address_id')->unsigned()->nullable();
			$table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null')->onUpdate('set null');
			$table->string('number_home')->nullable();
			$table->string('complement')->nullable();
			$table->string('name');
			$table->string('email')->nullable();
			$table->string('cellphone', 12)->nullable();
			$table->string('cpf', 11);
			$table->date('birthday')->nullable();
			$table->text('schedules')->nullable();
			$table->enum('sex', [
				'F',
				'M',
			])->default('F');
			$table->string('crm')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('doctors');
	}
}
