<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('order_no', 15)->unique();
            $table->enum('payment_type', ['paypal']);
            $table->string('payment_no', 60)->nullable()->comment('与订单关联的支付ID, 如PayPal的payment_id');
            $table->decimal('price')->comment('原价');
            $table->decimal('discount')->nullable()->comment('折扣');
            $table->decimal('pay_amount')->nullable();
            $table->decimal('paid_amount')->nullable();
            $table->decimal('refund_amount')->nullable();
            $table->enum('status', ['unpay', 'paid', 'refunded'])->default('unpay');
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
        Schema::drop('orders');
    }
}
