<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Traits\HandlesImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    use HandlesImages;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $products = Product::with(['store.photo', 'photos'])->paginate($perPage);

        return $this->sendResponse(
            ProductResource::collection($products)->response()->getData(true),
            'Products retrieved successfully'
        );
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
                if (!$photoFile->isValid()) continue;

                $path = $photoFile->store('photos/products', 'public');
                $this->createSmallImage($path);

                $product->photos()->create(['path' => $path]);
            }
        }

        $product->load('photos', 'store.photo');

        return $this->sendResponse(
            new ProductResource($product),
            'Product created successfully'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('store.photo', 'photos');

        return $this->sendResponse(
            new ProductResource($product),
            'Product details retrieved'
        );
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
                $this->deleteImageAndSmall($oldPhoto->getRawOriginal('path'));
                $oldPhoto->delete();
            }

            foreach ($request->file('photos') as $photoFile) {
                if (!$photoFile->isValid()) continue;

                $path = $photoFile->store('photos/products', 'public');
                $this->createSmallImage($path);

                $product->photos()->create(['path' => $path]);
            }
        }

        $product->load('photos', 'store.photo');

        return $this->sendResponse(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        foreach ($product->photos as $photo) {
            $this->deleteImageAndSmall($photo->getRawOriginal('path'));
            $photo->delete();
        }

        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully');
    }
}
