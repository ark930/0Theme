<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaypalPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paypal_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->string('payment_id', 50)->comment('PayPal payment id');
            $table->string('payment_method')->comment('支付方式, 包括direct credit card payments, stored credit card payments, PayPal account payments');
            $table->enum('intent', ['sale', 'authorize', 'order'])->commont('支付意图, sale: 立即支付; authorize a payment for capture later, order: 不知道;');
            $table->decimal('amount')->comment('支付金额');
            $table->char('currency', 3)->comment('支付货币代码');
            $table->string('payment_state', 20)->comment('支付状态, created: 交易成功创建; approved: 购买者确认交易; failed: 交易请求失败');
            $table->json('create_json')->comment('Create Payment时返回的原始数据');
            $table->string('approval_url')->comment('返回给购买者的支付链接');
            $table->timestamp('payment_create_at')->comment('交易创建时间');

            $table->json('execute_json')->nullable()->comment('Execute Payment时返回的原始数据');
            $table->string('payer_id', 50)->nullable()->comment('付款人ID');
            $table->string('sale_id', 50)->nullable();
            $table->string('sale_state', 20)->nullable()->comment('sale状态, completed: The transaction has completed.; partially_refunded: The transaction was partially refunded; pending: The transaction is pending; refunded:The transaction was fully refunded; denied: The transaction was denied');
            $table->decimal('transaction_fee')->nullable()->comment('PayPal收取的交易费用');
            $table->timestamp('payment_execute_at')->nullable()->comment('交易执行时间');

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
        Schema::drop('paypal_payments');
    }
}
