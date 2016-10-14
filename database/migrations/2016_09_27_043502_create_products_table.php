<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price');
            $table->enum('type', ['theme', 'pro', 'lifetime']);
            $table->unsignedBigInteger('theme_id')->nullable();
            $table->enum('period_of_validity', ['one_year', 'lifetime']);
            $table->boolean('for_sale')->default(true);
            $table->timestamps();
        });

        DB::update('ALTER TABLE products AUTO_INCREMENT = 1001;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
    }
}
