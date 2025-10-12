<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Traits\HandlesImages;
use Illuminate\Support\Facades\Storage;


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
            $photoFiles = $request->file('photos');

            foreach ($photoFiles as $photoFile) {
                if (! $photoFile->isValid()) {
                    continue;
                }

                $path = $photoFile->store('photos/products', 'public');

                if (! Storage::disk('public')->exists($this->getSmallImagePath($path))) {
                    $this->createSmallImage($path);
                }

                $product->photos()->create([
                    'path' => $path,
                ]);
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
            foreach ($product->photos as $oldPhoto) {
                $this->deleteImageAndSmall($oldPhoto->path);
                $oldPhoto->delete();
            }

            foreach ($request->file('photos') as $photoFile) {
                if (! $photoFile->isValid()) continue;

                $path = $photoFile->store('photos/products', 'public');

                if (! Storage::disk('public')->exists($this->getSmallImagePath($path))) {
                    $this->createSmallImage($path);
                }

                $product->photos()->create([
                    'path' => $path,
                ]);
            }
        }

        $product->load('photos', 'store');

        return $this->sendResponse($product, 'Product updated successfully');
    }

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
