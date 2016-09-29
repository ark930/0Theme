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
            $table->timestamp('activate_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('expire_at')->nullable();
            $table->string('theme_key', 24)->nullable()->collation('utf8_bin');
            $table->boolean('activate')->default(false)->comment('用于的主题是否激活');
            $table->string('deactivate_reason')->nullable()->comment('如果失效, 注明失效原因');

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
