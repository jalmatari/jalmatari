<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->tinyInteger('type')->default(0);
            $table->string('middleware', 10)->nullable();
            $table->integer('controller_id')->nullable();
            $table->string('action', 50);
            $table->string('route', 100)->nullable();
            $table->tinyInteger('id_required')->nullable()->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('routes');
    }
}
