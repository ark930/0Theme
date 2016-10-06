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
            $table->char('email_confirm_code', 30)->unique()->nullable()->comment('确认邮件代码');
            $table->rememberToken();
            $table->enum('membership', ['free', 'basic', 'pro', 'lifetime'])->default('free')->comment('用户等级');
            $table->char('secret_key', 30)->unique()->nullable()->comment('用户更新主题时需要用到的密钥');
            $table->timestamp('basic_from')->nullable();
            $table->timestamp('basic_to')->nullable();
            $table->timestamp('pro_from')->nullable();
            $table->timestamp('pro_to')->nullable();
            $table->timestamp('register_at')->nullable()->comment('用户是否完成注册(点击邮箱中的确认邮件)');
            $table->timestamp('first_login_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 15)->nullable();
            $table->unsignedBigInteger('inviter_id')->nullable()->comment('邀请人 user_id');
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
