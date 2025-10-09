<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
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

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $store = Store::create($validated);

        return $this->sendResponse($store,'store created successfully');
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
            $originalLogo = $store->getOriginal('logo');

            if ($originalLogo && Storage::disk('public')->exists($originalLogo)) {
                Storage::disk('public')->delete($originalLogo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        if (!empty($validated)) {
            $store->update($validated);
        }

        return $this->sendResponse($store, 'store updated successfully');
    }    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();

        if ($store->logo && Storage::disk('public')->exists($store->logo)) {
            Storage::disk('public')->delete($store->logo);
        }

        return $this->sendResponse([],'store deleted successfully');
    }
}
