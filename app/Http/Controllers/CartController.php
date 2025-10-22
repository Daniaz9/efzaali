<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\OrderResource;
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

        if (!$cart || $cart->items->isEmpty()) {
            return $this->sendResponse([], 'Your cart is empty');
        }

        // Paginate cart items (e.g., 10 per page)
        $perPage = $request->query('per_page', 10);

        $paginatedItems = $cart->items()->with('product.store', 'product.photos')
            ->paginate($perPage);

        return $this->sendResponse(
            CartItemResource::collection($paginatedItems)->response()->getData(true),
            'Cart retrieved successfully'
        );
    }
    public function store(StoreCartRequest $request)
    {
        $item = $this->cartService->addItem(
            $request->user(),
            $request->product_id,
            $request->quantity ?? 1
        );

        $item->load('product.store.photo', 'product.photos');

        return $this->sendResponse(
            new CartItemResource($item),
            'Item added to cart'
        );
    }

    public function update(UpdateCartRequest $request, $id)
    {
        $item = $this->cartService->updateItem(
            $request->user(),
            $id,
            $request->quantity
        );

        if (!$item) {
            return $this->sendResponse([], 'Item removed');
        }

        $item->load('product.store.photo', 'product.photos');

        return $this->sendResponse(
            new CartItemResource($item),
            'Item updated'
        );
    }

    public function destroy(Request $request, $id)
    {
        $this->cartService->removeItem($request->user(), $id);

        return $this->sendResponse([], 'Item removed from cart');
    }

    public function checkout(CheckoutRequest $request)
    {
        $user = $request->user();
        $perPage = $request->query('per_page', 10);
        $deliveryType = $request->delivery_type; // store or custom
        $useSaved = $request->boolean('use_saved_location', false);

        // Determine dropoff location
        $dropoff = $useSaved
            ? [
                'address' => $user->address,
                'lat'     => $user->lat,
                'long'    => $user->long,
            ]
            : [
                'address' => $request->dropoff_address,
                'lat'     => $request->dropoff_lat,
                'long'    => $request->dropoff_long,
            ];

        if ($deliveryType === 'store') {
            // Store delivery uses cart
            $paginatedOrders = $this->cartService->checkoutStoreOrder(
                $user,
                $dropoff,
                ['description' => $request->description ?? null],
                $perPage
            );
        } else {
            // Custom delivery does not use cart
            $paginatedOrders = $this->cartService->checkoutCustomDelivery(
                $user,
                $dropoff,
                [
                    'description'   => $request->description ?? null,
                    'pickup_address'=> $request->pickup_address,
                    'pickup_lat'    => $request->pickup_lat,
                    'pickup_long'   => $request->pickup_long,
                ],
                $perPage
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Checkout completed successfully',
            'orders'  => OrderResource::collection($paginatedOrders),
        ]);
    }
}
