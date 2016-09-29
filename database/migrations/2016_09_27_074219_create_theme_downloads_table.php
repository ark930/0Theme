<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemeDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_downloads', function (Blueprint $table) {
            $table->unsignedBigInteger('theme_version_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('download_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('ip', '15');
            $table->timestamps();

            $table->unique('theme_version_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('theme_downloads');
    }
}
