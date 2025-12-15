<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdvertisementResource;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index()
    {
        $ads = Advertisement::with('store.photo')->get();

        return $this->sendResponse(AdvertisementResource::collection($ads),'reels fetched successfully');
    }
}
