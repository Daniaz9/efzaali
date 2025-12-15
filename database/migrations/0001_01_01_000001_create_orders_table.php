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
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('type')->default(\App\Enums\OrderType::CUSTOM);
            $table->string('delivery_type')->default(\App\Enums\DeliveryType::FAST);
            $table->string('status')->default(\App\Enums\OrderStatus::PENDING);
            $table->string('pickup_address');
            $table->string('dropoff_address')->nullable();
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_long', 10, 7);
            $table->decimal('dropoff_lat', 10, 7)->nullable();
            $table->decimal('dropoff_long', 10, 7)->nullable();
            $table->decimal('delivery_fee', 8, 2)->nullable();
            $table->decimal('total_price', 8, 2)->nullable();
            $table->text('description')->nullable();//for custom delivery
//            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('driver_assigned_at')->nullable();
//            $table->timestamp('preparing_at')->nullable();
//            $table->timestamp('ready_for_pickup_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('on_the_way_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
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
