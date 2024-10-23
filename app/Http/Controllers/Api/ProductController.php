<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductsRequest\IndexProductRequest;
use App\Http\Requests\Api\ProductsRequest\StoreProductRequest;
use App\Http\Requests\Api\ProductsRequest\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(public ProductService $productService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductRequest $request)
    {
        $page = $request->get('page', 1);
        $name = $request->input('name', '');
        $minPrice = $request->input('min_price', '');
        $maxPrice = $request->input('max_price', '');

        $cacheKey = "products_page_{$page}_name_{$name}_min_{$minPrice}_max_{$maxPrice}";

//        $query = Product::latest()
//            ->search($name)
//            ->priceRange($minPrice, $maxPrice);
//        $sql = $query->toSql();
//        $bindings = $query->getBindings();
//        dd($sql, $bindings);

        $results = Cache::remember($cacheKey, 5, function () use ($name, $minPrice, $maxPrice) {
            return Product::latest()
                ->search($name)
                ->priceRange($minPrice, $maxPrice)
                ->paginate(10);
        });

        return ProductResource::collection($results)->additional([
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'total' => $results->total(),
                'per_page' => $results->perPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = $this->productService->store(data: $request->validated());
            DB::commit();
            return ProductResource::make($product)->additional([
                'message' => 'Product created successfully.'
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
        }
        return response()->json([
            'message' => $e->getMessage()
        ], Response::HTTP_BAD_REQUEST);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();
            $this->productService->update(data: $request->validated(), product: $product);
            DB::commit();
            return ProductResource::make($product->refresh())->additional([
                'message' => 'Product updated successfully.'
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
        }
        return response()->json([
            'message' => $e->getMessage()
        ], Response::HTTP_BAD_REQUEST);
    }

}
