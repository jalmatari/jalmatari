<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateUsersInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_info', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->integer('user_id');
            $table->text('details')->nullable();
            $table->integer('parent_id')->nullable()->default(0);
            $table->integer('day')->nullable()->default(0);
            $table->integer('month')->nullable()->default(0);
            $table->integer('year')->nullable()->default(0);
            $table->string('education', 255)->nullable();
            $table->string('job', 255)->nullable();
            $table->tinyInteger('sex')->nullable()->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_info');
    }
}
