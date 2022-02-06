<?php
namespace App\Http\Controllers\Admin;

use App\Order;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ChartResource;

class DashboardController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chart()
    {
        Gate::authorize('view', 'orders');
        $orders = Order::query()
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m-%d') as date , sum(order_items.quantity*order_items.price) as sum")
                ->groupBy('date')
                ->get();

        return ChartResource::collection($orders);
    }
}
