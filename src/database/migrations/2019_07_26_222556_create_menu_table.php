<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->integer('parent')->nullable()->default(0);
            $table->string('title', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->string('icon', 255)->nullable();
            $table->integer('ord')->nullable()->default(1);
            $table->tinyInteger('status')->nullable()->default(1);

            $table->unique('id', 'id');



        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
}
