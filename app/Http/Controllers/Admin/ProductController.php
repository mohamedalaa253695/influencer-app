<?php
namespace App\Http\Controllers\Admin;

use App\Events\ProductUpdatedEvent;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductCreateRequest;
use App\Jobs\ProductCreated;
use App\Jobs\ProductDeleted;
use App\Jobs\ProductUpdated;
use Symfony\Component\HttpFoundation\Response;

class ProductController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view', 'products');
        $products = Product::paginate(15);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductCreateRequest $request)
    {
        Gate::authorize('edit', 'products');

        $product = Product::create($request->only('title', 'description', 'image', 'price'));

        event(new ProductUpdatedEvent());

        ProductCreated::dispatch($product->toArray())->onQueue('checkout_queue');

        return response($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        Gate::authorize('view', 'products');

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        Gate::authorize('edit', 'products');

        $product->update($request->only('title', 'description', 'image', 'price'));

        event(new ProductUpdatedEvent());
        ProductUpdated::dispatch($product->toArray())->onQueue('checkout_queue');

        return response($product, Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        Gate::authorize('edit', 'products');

        Product::destroy($product->id);

        ProductDeleted::dispatch($product->id)->onQueue('checkout_queue');

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
