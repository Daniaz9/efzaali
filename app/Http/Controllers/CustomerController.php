<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function myOrders(Request $request)
    {
        $customer = $request->user();

        $orders = Order::where('customer_id', $customer->id)
            ->with('products.store', 'driver')
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders)
            ->additional(['message' => "List of customer orders", 'success' => true]);    }

    public function showOrder(Request $request, $id)
    {
        $customer = $request->user();

        $order = Order::where('customer_id', $customer->id)
            ->with('offers.driver', 'products.store','driver')
            ->findOrFail($id);

        return $this->sendResponse(new OrderResource($order),'');
    }

    public function accept($offerId)
    {
        $offer = Offer::with('order')->findOrFail($offerId);
        $order = $offer->order;

        if (!$order) {
            return $this->sendError('Offer has no associated order', 404);
        }

        if ($order->driver_id) {
            return $this->sendError('Order already assigned to a driver');
        }

        DB::transaction(function () use ($order, $offer) {
            $order->update([
                'driver_id' => $offer->user_id,
                'delivery_fee' => $offer->price,
                'total_price' => $order->total_price + $offer->price,
                'status' => \App\Enums\OrderStatus::ASSIGNED,
                'driver_assigned_at' => now(),
            ]);

            $order->offers()
                ->where('id', '!=', $offer->id)
                ->update(['is_accepted' => false, 'rejected_at' => now()]);

            $offer->update(['is_accepted' => true, 'accepted_at' => now()]);
        });

        $order->load('driver', 'offers.driver');

        return $this->sendResponse(new OrderResource($order), 'Offer accepted successfully');
    }

    public function cancelOrder(Request $request, $id)
    {
        $customer = $request->user();

        $order = Order::where('customer_id', $customer->id)
            ->findOrFail($id);

        if (!in_array($order->status, [
            \App\Enums\OrderStatus::PENDING,
//            \App\Enums\OrderStatus::ASSIGNED
        ]))
        {
            return $this->sendError('Order cannot be canceled now');
        }

        $order->update([
            'status' => \App\Enums\OrderStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);

        return $this->sendResponse($order,'Order cancelled');
    }

}
