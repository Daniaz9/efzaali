<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Traits\HandlesImages;
use Illuminate\Http\Request;


class StoreController extends Controller
{
    use HandlesImages;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::with('photo')->paginate(10);

        return $this->sendResponse(
            StoreResource::collection($stores)->response()->getData(true),
            'Stores retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreRequest $request)
    {
        $validated = $request->validated();

        $store = Store::create($validated);

        if ($request->hasFile('logo')) {
            $filePath = $request->file('logo')->store('photos/stores', 'public');
            $this->createSmallImage($filePath);

            $store->photo()->create([
                'path' => $filePath,
            ]);
        }

        $store->load('photo');

        return $this->sendResponse(
            new StoreResource($store), 'Store created successfully'
        );
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, Store $store)
    {
        $store->load('photo'); // Load only photo normally

        // Paginate related products (e.g., 10 per page or user-defined)
        $perPage = $request->get('per_page', 10);
        $products = $store->products()->paginate($perPage);

        return $this->sendResponse([
            'store' => new StoreResource($store),
            'products' => $products, // includes pagination metadata
        ], 'Store details retrieved');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {

        if ($request->hasFile('logo')) {
            if ($store->photo) {
                $this->deleteImageAndSmall($store->photo->path);
                $store->photo()->delete();
            }

            $path = $request->file('logo')->store('photos/stores', 'public');
            $this->createSmallImage($path);

            $store->photo()->create(['path' => $path]);
        }

        if (!empty($validated)) {
            $store->update($validated);
        }

        $store->load('photo');

        return $this->sendResponse(
            new StoreResource($store), 'Store updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {

        if ($store->photo) {
            $this->deleteImageAndSmall($store->photo->path);
            $store->photo()->delete();
        }

        $store->delete();

        return $this->sendResponse([], 'Store deleted successfully');
    }
}
