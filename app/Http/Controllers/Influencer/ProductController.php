<?php
namespace App\Http\Controllers\Influencer;

use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request as HttpRequest;

class ProductController
{
    public function index(HttpRequest $request)
    {
        sleep(2);
        $query = Product::query();
        if ($s = $request->input('s')) {
            $query->whereRaw("title LIKE '%{$s}%'")
                ->orWhereRaw("description LIKE '%{$s}%'");
        }
        return ProductResource::collection($query->get());
    }
}
