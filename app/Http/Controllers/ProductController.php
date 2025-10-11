<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Traits\HandlesImages;


class ProductController extends Controller
{
    use HandlesImages;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['store','photos'])->get();
        return $this->sendResponse($products, 'all products retrieved');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $product = Product::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                if (! $photoFile->isValid()) continue;

                // store main image (relative path)
                $path = $photoFile->store('photos/products', 'public');

                // create small copy (same folder, filename-small.ext)
                $this->createSmallImage($path);

                // save DB record with main path
                $product->photos()->create(['path' => $path]);
            }
        }

        $product->load('photos', 'store');

        return $this->sendResponse($product, 'Product created successfully');
    }
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('store');
        return $this->sendResponse($product, 'product details retrieved');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        if (!empty($validated)) {
            $product->update($validated);
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                if (! $photoFile->isValid()) continue;

                $path = $photoFile->store('photos/products', 'public');
                $this->createSmallImage($path);
                $product->photos()->create(['path' => $path]);
            }
        }

        $product->load('photos', 'store');

        return $this->sendResponse($product, 'Product updated successfully');
    }    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // delete all related photos (files + DB rows)
        foreach ($product->photos as $photo) {
            $raw = $photo->getRawOriginal('path'); // raw DB value
            $this->deleteImageAndSmall($raw);
            $photo->delete();
        }

        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully');
    }
}
