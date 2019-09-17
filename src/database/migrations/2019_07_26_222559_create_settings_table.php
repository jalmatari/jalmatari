<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->string('name', 200)->nullable();
            $table->string('desc', 200)->nullable();
            $table->enum('type', ['text', 'checkbox', 'textarea', 'multi', 'editor', 'article', 'list', 'multi_list', 'hidden'])->nullable()->default('text');
            $table->text('value')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('section', 20)->nullable()->default('site');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
