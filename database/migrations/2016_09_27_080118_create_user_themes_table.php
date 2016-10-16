<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_themes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('theme_id');
            $table->timestamp('basic_from')->nullable();
            $table->timestamp('basic_to')->nullable();
            $table->boolean('is_deactivate')->default(false)->comment('表示主题是否失效。 如主题激活的网站过多, 系统判断为非法使用, 将主题设置为失效');
            $table->string('deactivate_reason')->nullable()->comment('如果失效、过期, 注明原因');
            $table->timestamps();

            $table->unique(['user_id', 'theme_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_themes');
    }
}
