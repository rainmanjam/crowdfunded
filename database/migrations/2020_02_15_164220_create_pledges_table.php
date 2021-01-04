<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pledges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_trans_id')->nullable();
            $table->string('stripe_setup_intent_id')->nullable();
            $table->string('stripe_payment_intent')->nullable();
            $table->integer('pledge_amount');
            $table->integer('token_amount')->default(1);
            $table->string('pledge_level')->default('customer'); //customer, creator, developer
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('display_name')->nullable();
            $table->string('message')->nullable();
            $table->boolean('newsletter_join')->default(1);
            $table->boolean('forum_join')->default(1);
            $table->boolean('has_been_charged')->default(0); //0 = no, 1 = yes
            $table->string('token')->nullable();
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
        Schema::dropIfExists('pledges');
    }
}
