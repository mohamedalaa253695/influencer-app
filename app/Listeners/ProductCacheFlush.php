<?php
namespace App\Listeners;

use Illuminate\Support\Facades\Cache;

class ProductCacheFlush
{
    public function handle($event)
    {
        Cache::forget('products');
    }
}
