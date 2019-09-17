<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->string('name', 500);
            $table->string('title', 500);
            $table->string('new', 500)->default('جديد');
            $table->string('edit', 500)->default('تعديل');
            $table->tinyInteger('show_add_btn')->nullable()->default(0);
            $table->tinyInteger('status')->nullable()->default(0);
            $table->string('namespace', 500)->nullable()->default('');
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
        Schema::dropIfExists('tables');
    }
}
