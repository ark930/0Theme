<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemeVersionTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_version_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('theme_version_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->unique(['theme_version_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('theme_version_tags');
    }
}
