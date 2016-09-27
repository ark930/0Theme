<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('author', 50);
            $table->string('version', 20);
            $table->text('description');
            $table->string('logo');
            $table->timestamp('release_at')->nullable();
            $table->string('demo_url');
            $table->string('lite_url');
            $table->text('content');
            $table->string('requirement');
            $table->string('store_at')->comment('主题存储位置');
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
        Schema::drop('themes');
    }
}
