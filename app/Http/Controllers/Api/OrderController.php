<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrdersRequest\ShowOrderRequest;
use App\Http\Requests\Api\OrdersRequest\StoreOrderRequest;
use App\Http\Requests\Api\OrdersRequest\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService)
    {
    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);

        $cacheKey = "orders_page_{$page}";

        $results = Cache::remember($cacheKey, 5, function () {
            return Order::latest()
                ->myOrders()
                ->paginate(10);
        });

        return OrderResource::collection($results)->additional([
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'total' => $results->total(),
                'per_page' => $results->perPage(),
            ]
        ]);
    }

    public function show(ShowOrderRequest $request, Order $order)
    {
        $order = $this->orderService->show(order: $order);

        return OrderResource::make($order)->additional([
            'message' => 'Order created successfully.'
        ])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = $this->orderService->store(data: $request->validated());
            DB::commit();
            return OrderResource::make($order)->additional([
                'message' => 'Order created successfully.'
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
        }
        return response()->json([
            'message' => $e->getMessage()
        ], Response::HTTP_BAD_REQUEST);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        try {
            DB::beginTransaction();
            $this->orderService->update(data: $request->validated(), order: $order);
            DB::commit();
            return OrderResource::make($order->refresh())->additional([
                'message' => 'Order updated successfully.'
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
        }
        return response()->json([
            'message' => $e->getMessage()
        ], Response::HTTP_BAD_REQUEST);
    }

}
