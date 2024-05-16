<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('giftdetails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')->constrained('orderproducts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_address');
            $table->string('note')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giftdetails');
    }
};
