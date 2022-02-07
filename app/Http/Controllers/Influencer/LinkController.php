<?php
namespace App\Http\Controllers\Influencer;

use App\Http\Resources\LinkResource;
use App\Link;
use App\LinkProduct;
use Illuminate\Http\Request;
use Str;

class LinkController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->user('api'));
        $link = Link::create([
            'user_id' => $request->user('api')->id,
            'code' => Str::random(6)
        ]);
        // dd($request->user());
        foreach ($request->input('products') as $product_id) {
            LinkProduct::create([
                'link_id' => $link->id,
                'product_id' => $product_id
            ]);
        }

        return new LinkResource($link);
    }
}
