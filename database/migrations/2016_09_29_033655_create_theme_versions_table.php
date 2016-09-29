<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemeVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('theme_id');
            $table->string('author', 50);
            $table->string('version', 20);
            $table->text('description');
            $table->string('logo');
            $table->timestamp('release_at');
            $table->string('demo_url');
            $table->string('lite_url');
            $table->text('content');
            $table->string('requirement');
            $table->string('premium_store_at')->comment('主题存储位置');
            $table->enum('premium_store_type', ['local', 'url'])->comment('主题存储形式');
            $table->char('premium_sha1', 40);
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
        Schema::drop('theme_versions');
    }
}
