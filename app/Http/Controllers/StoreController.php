<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Traits\HandlesImages;


class StoreController extends Controller
{
    use HandlesImages;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores=Store::all();
        return $this->sendResponse($stores,'all stores retrieved');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreRequest $request)
    {
        $validated = $request->validated();

        $store = Store::create($validated);

        if ($request->hasFile('logo')) {
            // Store main image
            $filePath = $request->file('logo')->store('photos/stores', 'public');

            // Create the small version
            $this->createSmallImage($filePath);

            // Attach one photo record to the store
            $store->photo()->create([
                'path' => $filePath,
            ]);
        }

        $store->load('photo');

        return $this->sendResponse($store, 'Store created successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
//        if (!$store)
//            return $this->sendError('');
        return $this->sendResponse($store,'store details retrieved');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            if ($store->photo) {
                $this->deleteImageAndSmall($store->photo->path);
                $store->photo()->delete();
            }

            $path = $request->file('logo')->store('photos/stores', 'public');

            $this->createSmallImage($path);

            $store->photo()->create([
                'path' => $path,
            ]);
        }

        if (!empty($validated)) {
            $store->update($validated);
        }

        $store->load('photo');

        return $this->sendResponse($store, 'Store updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $originalLogo = $store->getOriginal('logo');
        $this->deleteImageAndSmall($originalLogo);

        $store->delete();

        return $this->sendResponse([], 'Store deleted successfully');
    }
}
