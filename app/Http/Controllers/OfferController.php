<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($orderId)
    {
            $order = Order::with('offers.driver')->findOrFail($orderId);
            return $this->sendResponse($order->offers,'');
    }

    public function store(Request $request, $orderId)
    {
        $request->validate([
            'price' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
        ]);

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

    public function accept($orderId, $offerId)
    {
        $order = Order::findOrFail($orderId);
        $offer = $order->offers()->findOrFail($offerId);

        if ($order->driver_id) {
            return $this->sendError('Order already assigned to a driver');
        }

        DB::transaction(function () use ($order, $offer) {
            $order->update([
                'driver_id' => $offer->user_id,
                'delivery_fee' => $offer->price,
                'total_price' => $order->total_price + $offer->price,
                'status' => \App\Enums\OrderStatus::CONFIRMED,
                'driver_assigned_at' => now(),
            ]);

            $order->offers()->where('id', '!=', $offer->id)
                ->update(['is_accepted' => false, 'rejected_at' => now()]);

            $offer->update(['is_accepted' => true, 'accepted_at' => now()]);
        });

        return $this->sendResponse($order->load('driver', 'offers.driver'),'Offer accepted successfully');
    }

}
