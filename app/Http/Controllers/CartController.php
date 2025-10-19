<?php

namespace App\Http\Controllers;

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
        if ($cart== null)
            return $this->sendResponse([], 'Your Cart is empty');

        return $this->sendResponse($cart, '');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $item = $this->cartService->addItem(
            $request->user(),
            $request->product_id,
            $request->quantity ?? 1
        );

        return $this->sendResponse($item, 'Item added to cart');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $item = $this->cartService->updateItem($request->user(), $id, $request->quantity);

        return $this->sendResponse($item, $item ? 'Item updated' : 'Item removed');
    }

    public function destroy(Request $request, $id)
    {
        $this->cartService->removeItem($request->user(), $id);
        return $this->sendResponse([],'Item removed');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'dropoff_address' => 'required|string|max:255',
            'dropoff_lat' => 'required|numeric',
            'dropoff_long' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ]);

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
