<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCartForUser($user): Cart
    {
        return Cart::firstOrCreate(['customer_id' => $user->id]);
    }

    public function getCartForUser($user): ?Cart
    {
        return Cart::where('customer_id', $user->id)
            ->with('items.product.store')
            ->first();
    }

    public function addItem($user, int $productId, int $quantity = 1): CartItem
    {
        $cart = $this->getOrCreateCartForUser($user);
        $product = Product::findOrFail($productId);
        $price = $product->price;

        $item = $cart->items()->where('product_id', $productId)->first();
        if ($item) {
            $item->quantity += $quantity;

            // Optionally update price snapshot if product price changed
            $item->price = $price;
            $item->save();
        } else {
            $item = $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }

        return $item->load('product.store');
    }

    public function updateItem($user, int $itemId, int $quantity): ?CartItem
    {
        $cart = $this->getOrCreateCartForUser($user);
        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        if ($quantity <= 0) {
            $item->delete();
            return null;
        }

        $item->quantity = $quantity;
        $item->save();

        return $item->load('product.store');
    }

    public function removeItem($user, int $itemId): bool
    {
        $cart = $this->getOrCreateCartForUser($user);
        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->delete();
        return true;
    }

    /**
     * Checkout the cart to a store‐type order.
     *
     * @param  \App\Models\User  $user
     * @param  array  $dropoff {address, lat, long}
     * @param  array  $extra (e.g., description)
     * @return \App\Models\Order
     */
    public function checkoutStoreOrder($user, array $dropoff, array $extra = [], int $perPage = 10)
    {
        $cart = $this->getCartForUser($user);

        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        $order = DB::transaction(function () use ($user, $cart, $dropoff, $extra) {
            $total = $cart->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            $order = Order::create([
                'customer_id' => $user->id,
                'type' => \App\Enums\OrderType::STORE,
                'delivery_type' => \App\Enums\DeliveryType::SLOW,
                'status' => \App\Enums\OrderStatus::PENDING,
                'pickup_address' => $cart->items->first()->product->store->address,
                'pickup_lat' => $cart->items->first()->product->store->lat,
                'pickup_long' => $cart->items->first()->product->store->long,
                'dropoff_address' => $dropoff['address'],
                'dropoff_lat' => $dropoff['lat'],
                'dropoff_long' => $dropoff['long'],
                'delivery_fee' => null,
                'total_price' => $total,
                'description' => $extra['description'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $order->products()->attach($item->product_id, [
                    'quantity' => $item->quantity,
                    'total_price' => $item->quantity * $item->price,
                ]);
            }

            $cart->items()->delete();
            return $order;
        });

        return Order::with('products.store', 'customer')
            ->where('id', $order->id)
            ->paginate($perPage);
    }

    public function checkoutCustomDelivery($user, array $dropoff, array $extra = [], int $perPage = 10)
    {
        $order = Order::create([
            'customer_id' => $user->id,
            'type' => \App\Enums\OrderType::CUSTOM, // custom delivery
            'delivery_type' => \App\Enums\DeliveryType::SLOW,
            'status' => \App\Enums\OrderStatus::PENDING,
            'pickup_address' => $extra['pickup_address'] ?? null,
            'pickup_lat' => $extra['pickup_lat'] ?? null,
            'pickup_long' => $extra['pickup_long'] ?? null,
            'dropoff_address' => $dropoff['address'],
            'dropoff_lat' => $dropoff['lat'],
            'dropoff_long' => $dropoff['long'],
            'delivery_fee' => null,
            'total_price' => null, // not calculated yet
            'description' => $extra['description'] ?? null,
        ]);

        return Order::with('customer')->where('id', $order->id)->paginate($perPage);
    }


}

