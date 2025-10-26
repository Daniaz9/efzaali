<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferRequest;
use App\Http\Resources\OfferResource;
use App\Http\Resources\OrderResource;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function openOrders(Request $request)
    {
        $driver = $request->user();
        $maxDistance = $request->max_distance; // optional, in km

        if (!$driver->lat || !$driver->long) {
            return $this->sendError('Driver location not set.');
        }

        $orders = Order::openOrder()
            ->nearestToDriver($driver->lat, $driver->long, $maxDistance)
            ->with(['customer', 'offers'])
            ->simplePaginate(10);

        if ($orders->isEmpty()) {
            return $this->sendResponse([], 'No orders available');
        }

        return OrderResource::collection($orders)
            ->additional(['message' => "List of available orders", 'success' => true]);
    }

    public function myOffers(Request $request)
    {
        $driver = $request->user();

        $offers = Offer::where('user_id', $driver->id)
            ->with('order.customer')
            ->latest()
            ->paginate(10);

        return OfferResource::collection($offers)
            ->additional(['message' => "List of driver's offers", 'success' => true]);
    }

    public function myOrders(Request $request)
    {
        $driver = $request->user();

        $orders = Order::where('driver_id', $driver->id)
            ->with('customer', 'products.store')
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders)
            ->additional(['message' => "List of driver's orders", 'success' => true]);
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
            'average_delivery_time'=> $request->average_delivery_time ,
            'message' => $request->message,
        ]);
        $offer->load('driver','order');

        return $this->sendResponse(new OfferResource($offer),'Offer submitted successfully');
    }

    public function updateStatus(Request $request, $orderId)
    {
        $driver = $request->user();

        $order = Order::where('id', $orderId)
            ->where('driver_id', $driver->id)
            ->first();

        if ($order == null) {
            return $this->sendResponse([], 'The order was canceled or deleted');
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
        $order->status = $request->status; // ✅ Fix: don’t uppercase it
        $order->save();

        return $this->sendResponse(new OrderResource($order), 'Order status updated');
    }

    public function changeAvailability(Request $request)
    {
        $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $driver = auth()->user();
        $driver->update([
            'is_available' => $request->is_available,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Availability status updated',
            'is_available' => $driver->is_available,
        ]);
    }
}
