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
            $table->bigIncrements('id');
            $table->string('name')->unique()->nullble()->comment('用户名');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('registered')->default(false)->comment('用户是否完成注册(点击邮箱中的确认邮件)');
            $table->enum('membership', ['basic', 'pro', 'lifetime'])->nullable()->comment('用户等级');
            $table->timestamp('pro_from')->nullable();
            $table->timestamp('pro_to')->nullable();
            $table->timestamp('register_at')->nullable();
            $table->timestamp('first_login_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->unsignedBigInteger('inviter_id')->nullable()->comment('邀请人 user_id');
            $table->string('email_confirm_code', 24)->comment('确认邮件代码');
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
        Schema::drop('users');
    }
}
