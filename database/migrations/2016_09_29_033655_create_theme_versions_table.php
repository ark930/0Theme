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
            $table->string('author', 50)->nullable();
            $table->char('sha1', 40);
            $table->string('version', 20);
            $table->string('requirements');
            $table->string('document_url');
            $table->boolean('has_free');
            $table->string('free_url')->nullable();
            $table->text('description');
            $table->longText('changelog');
            $table->string('thumbnail');
            $table->string('thumbnail_tiny');
            $table->timestamp('release_at');
            $table->string('store_at')->comment('主题存储位置');
            $table->enum('store_type', ['local', 'url'])->comment('主题存储形式');
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
