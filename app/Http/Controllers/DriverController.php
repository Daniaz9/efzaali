<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferRequest;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function openOrders(Request $request)
    {
        $orders = Order::openOrder()
            ->with(['customer','offers'])
            ->latest()
            ->simplePaginate(10);
        if ($orders->isEmpty()) {
            return $this->sendResponse([], 'no orders available');
        }
        return $this->sendResponse($orders,'');
    }

    public function myOffers(Request $request)
    {
        $driver = $request->user();

        $offers = Offer::where('user_id', $driver->id)
            ->with('order.customer')
            ->latest()
            ->paginate(10);

        return $this->sendResponse($offers,"");
    }

    public function myOrders(Request $request)
    {
        $driver = $request->user();

        $orders = Order::where('driver_id', $driver->id)
            ->with('customer', 'products.product.store')
            ->latest()
            ->paginate(10);

        return $this->sendResponse($orders,"");
    }

    public function storeOffer(StoreOfferRequest $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->driver_id) {
            return $this->sendError('Order already assigned');
        }

        if ($order->offers()->where('user_id', auth()->id())->exists()) {
            return $this->sendError('You already made an offer for this order');
        }

        $offer = Offer::create([
            'order_id' => $orderId,
            'user_id' => auth()->id(),
            'price' => $request->price,
            'note' => $request->note,
        ]);

        return $this->sendResponse(['offer' => $offer->load('driver')],'');
    }

    public function updateStatus(Request $request, $orderId)
    {
        $driver = $request->user();

        $order = Order::where('id', $orderId)
            ->where('driver_id', $driver->id)
            ->first();

        if ($order== null) {
            return $this->sendResponse([], 'the order canceled or deleted');
        }

        $request->validate([
            'status' => 'required|string|in:picked_up,on_the_way,delivered',
        ]);

        $statusMap = [
            'picked_up' => 'picked_up_at',
            'on_the_way' => 'on_the_way_at',
            'delivered' => 'delivered_at',
        ];

        $timestampField = $statusMap[$request->status];
        $order->$timestampField = now();
        $order->status = strtoupper($request->status);
        $order->save();

        return $this->sendResponse($order,'Order status updated');
    }
}
