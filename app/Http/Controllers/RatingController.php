<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rating;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Requests\UpdateRatingRequest;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function rateUser(StoreRatingRequest $request, $orderId)
    {

        $user = $request->user();
        $order = Order::findOrFail($orderId);

        $rateeId = $request->ratee_type === 'driver'
            ? $order->driver_id
            : $order->customer_id;

        if (!$rateeId) {
            return $this->sendError('User to rate not found.');
        }

        $rating = Rating::updateOrCreate(
            [
                'order_id' => $order->id,
                'rater_id' => $user->id,
                'ratee_id' => $rateeId,
            ],
            [
                'stars' => $request->stars,
                'comment' => $request->comment,
            ]
        );

        return $this->sendResponse($rating,'Rating submitted successfully');
    }

}
