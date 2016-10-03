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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('theme_id');
            $table->string('theme_key', 15)->collation('utf8_bin');
            $table->timestamp('activate_at')->nullable()->comment('用户下载后激活主题的时间');
            $table->timestamp('basic_expire_at')->nullable()->comment('当用户为basic会员, 单独购买主题时, 主题过期的时间');
            $table->boolean('is_deactivate')->default(false)->comment('表示主题是否失效。 如主题激活的网站过多, 系统判断为非法使用, 将主题设置为失效');
            $table->string('deactivate_reason')->nullable()->comment('如果失效、过期, 注明原因');
            $table->timestamps();

            $table->primary(['user_id', 'theme_id']);
            $table->unique('theme_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_themes');
    }
}
