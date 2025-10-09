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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->enum('type',['fast','slow'])->default('fast');
            $table->enum('status',['delivered','processing','on_the_way','cancelled','pending'])->default('pending');
            $table->string('pickup_address');
            $table->string('dropoff_address');
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_long', 10, 7);
            $table->decimal('dropoff_lat', 10, 7);
            $table->decimal('dropoff_long', 10, 7);
            $table->decimal('distance', 8, 2);
            $table->decimal('delivery_fee', 8, 2);
            $table->decimal('total_price', 8, 2);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
