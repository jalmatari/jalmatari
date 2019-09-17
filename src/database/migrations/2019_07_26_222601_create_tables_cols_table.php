<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateTablesColsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables_cols', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->integer('TABLE_ID');
            $table->string('COLUMN_NAME', 64);
            $table->integer('ORDINAL_POSITION')->default(0);
            $table->longText('COLUMN_DEFAULT')->nullable();
            $table->string('IS_NULLABLE', 3);
            $table->string('DATA_TYPE', 64);
            $table->longText('COLUMN_TYPE');
            $table->string('EXTRA', 100);
            $table->string('COLUMN_COMMENT', 1024);
            $table->string('TITLE', 500)->nullable();
            $table->integer('TYPE')->default(0);
            $table->string('SOURCE', 1024)->nullable();
            $table->string('ATTR', 1024)->nullable();
            $table->tinyInteger('STATUS')->default(0);
            $table->tinyInteger('SHOW_IN_LIST')->default(1);
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
        Schema::dropIfExists('tables_cols');
    }
}
