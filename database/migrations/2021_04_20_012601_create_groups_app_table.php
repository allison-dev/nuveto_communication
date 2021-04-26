<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups_apps', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->integer('group_id');
            $table->string('priv_access');
            $table->string('priv_delete');
            $table->string('priv_export');
            $table->string('priv_insert');
            $table->string('priv_print');
            $table->string('priv_update');
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
        Schema::dropIfExists('groups_app');
    }
}
