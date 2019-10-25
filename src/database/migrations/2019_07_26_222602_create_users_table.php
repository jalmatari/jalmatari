<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->string('name', 100);
            $table->string('username', 20)->nullable();
            $table->string('password', 60)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 50);
            $table->string('city', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('photo', 100)->nullable();
            $table->integer('job_title')->nullable()->default(1);
            $table->tinyInteger('status')->nullable()->default(0);
            $table->string('permissions', 2000)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 60)->nullable();
            $table->string('api_token', 60)->nullable();
            $table->tinyInteger('created_by_app')->nullable()->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique('email', 'email');
            $table->unique('username', 'userName');



        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
