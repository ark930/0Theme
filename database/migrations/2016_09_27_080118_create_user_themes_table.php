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
            $table->timestamp('activate_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('主题激活的时间');
            $table->timestamp('expire_at')->nullable()->comment('主题过期的时间');
            $table->string('theme_key', 24)->nullable()->collation('utf8_bin');
            $table->boolean('is_activate')->default(false)->comment('表示主题是否激活');
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
