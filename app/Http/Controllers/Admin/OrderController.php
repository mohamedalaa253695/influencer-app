<?php
namespace App\Http\Controllers\Admin;

use App\Order;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Response;

class OrderController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view', 'orders');
        $orders = Order::paginate();
        return OrderResource::collection($orders);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        Gate::authorize('view', 'orders');
        return new OrderResource($order);
    }

    public function exportAsCsv()
    {
        Gate::authorize('view', 'orders');

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=orders.csv',
            'Pragma' => 'no-cache',
            'Cache-controll' => 'must-revalidate, post-check=0 , pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $orders = Order::all();
            $file = fopen('php://output', 'w');

            //Header Row
            fputcsv($file, ['ID', 'Name', 'Email', 'Product Title', 'Price', 'Quantity']);

            //Body
            foreach ($orders as $order) {
                fputcsv($file, [$order->id, $order->name, $order->email, '', '', '']);

                foreach ($order->orderItems as $orderItem) {
                    fputcsv($file, ['', '', '', $orderItem->product_titl, $orderItem->price, $orderItem->quantity]);
                }
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
