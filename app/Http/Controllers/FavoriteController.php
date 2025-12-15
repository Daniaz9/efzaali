<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, $productId)
    {
        $user = $request->user();

        // Check if already favorited
        if ($user->favorites()->where('product_id', $productId)->exists()) {
            // remove it
            $user->favorites()->detach($productId);

            return $this->sendResponse(['favorite' => false],'Removed from favorites');
        }

        // otherwise add it
        $user->favorites()->attach($productId);

        return $this->sendResponse(['favorite' => true],'Added to favorites');
    }

    public function myFavorites(Request $request)
    {
        $result=$request->user()->favorites()->with('brand', 'store')->get();
        return $this->sendResponse(ProductResource::collection($result),'Favorite products fetched successfully');
    }
}
