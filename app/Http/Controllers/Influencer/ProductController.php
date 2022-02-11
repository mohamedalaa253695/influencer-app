<?php
namespace App\Http\Controllers\Influencer;

use Cache;
use App\Product;
use Illuminate\Support\Str;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request as HttpRequest;

class ProductController
{
    public function index(HttpRequest $request)
    {
        $products = Cache::remember('products', 60 * 30, function () use ($request) {
            sleep(2);
            return Product::all();
        });

        if ($s = $request->input('s')) {
            $products = $products->filter(function (Product $product) use ($s) {
                return Str::contains($product->title, $s) || Str::contains($product->description, $s);
            });
        }

        return ProductResource::collection($products);
    }
}
