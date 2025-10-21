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
    public function checkoutToOrder($user, array $dropoff, array $extra = [])
    {
        $cart = $this->getCartForUser($user);
        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        // Group cart items by store
        $groups = $cart->items->groupBy(fn($item) => $item->product->store_id);

        return DB::transaction(function () use ($user, $groups, $dropoff,  $extra, $cart) {
            $createdOrders = collect();

            foreach ($groups as $storeId => $items) {
                $store = $items->first()->product->store;

                $subtotal = $items->sum(fn($i) => $i->quantity * $i->price);
//                $fee = $this->calculateDeliveryFee($store, $dropoff);

                $order = \App\Models\Order::create([
                    'customer_id' => $user->id,
                    'type' => \App\Enums\OrderType::STORE,
                    'delivery_type' => \App\Enums\DeliveryType::SLOW,
                    'status' => \App\Enums\OrderStatus::PENDING,
                    'pickup_address' => $store->address,
                    'dropoff_address' => $dropoff['address'],
                    'pickup_lat' => $store->lat,
                    'pickup_long' => $store->long,
                    'dropoff_lat' => $dropoff['lat'],
                    'dropoff_long' => $dropoff['long'],
                    'delivery_fee' => null,
                    'total_price' => $subtotal ,
                    'description' => $extra['description'] ?? null,
                ]);

                foreach ($items as $item) {
                    $order->products()->attach($item->product_id, [
                        'quantity' => $item->quantity,
                        'total_price' => $item->quantity * $item->price,
                    ]);
                }

                $createdOrders->push($order);
            }

            // Empty the cart
            $cart->items()->delete();

            return $createdOrders->map(function ($order) {
                return $order->load('products.store', 'customer');
            });
        });
    }

//    protected function calculateDeliveryFee($store, array $dropoff): float
//    {
//        // Implement your distance‐based fee or flat fee
//        return 5.00;
//    }
}

