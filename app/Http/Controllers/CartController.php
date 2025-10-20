<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cart = $this->cartService->getCartForUser($request->user());

        if ($cart === null) {
            return $this->sendResponse([], 'Your Cart is empty');
        }

        return $this->sendResponse($cart, '');
    }

    public function store(StoreCartRequest $request)
    {
        $item = $this->cartService->addItem(
            $request->user(),
            $request->product_id,
            $request->quantity ?? 1
        );

        return $this->sendResponse($item, 'Item added to cart');
    }

    public function update(UpdateCartRequest $request, $id)
    {
        $item = $this->cartService->updateItem(
            $request->user(),
            $id,
            $request->quantity
        );

        return $this->sendResponse($item, $item ? 'Item updated' : 'Item removed');
    }

    public function destroy(Request $request, $id)
    {
        $this->cartService->removeItem($request->user(), $id);

        return $this->sendResponse([], 'Item removed');
    }

    public function checkout(CheckoutRequest $request)
    {
        $orders = $this->cartService->checkoutToOrder(
            $request->user(),
            [
                'address' => $request->dropoff_address,
                'lat' => $request->dropoff_lat,
                'long' => $request->dropoff_long,
            ],
            ['description' => $request->description ?? null]
        );

        return $this->sendResponse($orders, 'Checkout completed successfully');
    }
}
