<?php

namespace App\Http\Controllers\Controllers;

use App\Events\ProductAdded;
use App\Filters\ProductFilter;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => ['required', 'string', Rule::unique('products', 'name')],
                'moved_at' => ['required', 'date'],
                'sku' => ['required', 'string', Rule::unique('products', 'sku')],
                'category_id' => ['nullable', 'integer', 'exists:categories,id'],
                'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
                'unit' => ['required', 'string'],
                'cost_price' => ['required', 'numeric', 'min:0'],
                'selling_price' => ['required', 'numeric', 'min:0'],
                'reorder_level' => ['required', 'integer', 'min:0'],
                'current_stock' => ['required', 'integer', 'min:0'],
            ]);

            $product = Product::query()->create($validatedData);
            $product->load([
                'category:id,name',
                'supplier:id,name',
            ]);

            broadcast(new ProductAdded($product))->toOthers();

            return new JsonResponse($product, 201);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $productId = $request->route('id');
            $validatedData = $request->validate([
                'sku' => 'required',
                'category_id' => 'required|exists:categories,id',
                'unit' => 'required',
                'cost_price' => 'required',
                'selling_price' => 'required',
                'reorder_level' => 'required',
                'current_stock' => 'required',
            ]);

            $product = Product::query()->find($productId);

            if (! $product) {
                return new JsonResponse(
                    [
                        'error' => 'Product not found',
                    ],
                    404
                );
            }
            $product = Product::query()
                ->where('id', $productId)
                ->update($validatedData);

            return new JsonResponse($product);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        $productId = $request->route('id');
        $product = Product::query()->find($productId);
        if (! $product) {
            return new JsonResponse(
                [
                    'error' => 'Product not found',
                ],
                404
            );
        }
        $product->delete();

        return new JsonResponse(null, 204);
    }

    public function show(Request $request): JsonResponse
    {
        $productId = $request->route('id');
        $product = Product::query()
            ->with([
                'category:id,name',
                'supplier:id,name',
            ])
            ->find($productId);

        if (! $product) {
            return new JsonResponse([
                'error' => 'Product not found',
            ], 404);
        }

        return new JsonResponse($product);
    }

    public function index(Request $request): JsonResponse
    {

        $productFilter = ProductFilter::createFromRequest($request);
        $productQuery = Product::query()
            ->with([
                'category:id,name',
                'supplier:id,name',
            ])
            ->orderByDesc('created_at');

        $page = max((int) $request->query('page', 1), 1);
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        if ($productFilter->name) {
            $productQuery = $productQuery->where('name', 'like', '%'.$productFilter->name.'%');
        }

        if ($productFilter->sku) {
            $productQuery = $productQuery->where('sku', 'like', '%'.$productFilter->sku.'%');
        }

        if ($productFilter->productId) {
            $productQuery = $productQuery->where('id', '=', $productFilter->productId);
        }

        if ($productFilter->movedAt) {
            $productQuery = $productQuery->where('moved_at', '=', $productFilter->movedAt);
        }

        if ($productFilter->categoryId) {
            $productQuery = $productQuery->where('category_id', '=', $productFilter->categoryId);
        }

        if ($productFilter->supplierId) {
            $productQuery = $productQuery->where('supplier_id', '=', $productFilter->supplierId);
        }

        if ($productFilter->movementType) {
            $productQuery->whereHas('stockMovements', function ($q) use ($productFilter) {
                $q->where('movement_type', $productFilter->movementType);
            });
        }

        if ($productFilter->createdBefore) {
            $productQuery = $productQuery->whereDate('created_at', '<=', $productFilter->createdBefore);
        }

        if ($productFilter->createdAfter) {
            $productQuery = $productQuery->whereDate('created_at', '>=', $productFilter->createdAfter);
        }

        $products = $productQuery->paginate(
            perPage: $limit,
            columns: ['*'],
            pageName: 'page',
            page: $page,
        );

        return new JsonResponse(
            ['products' => $products]
        );
    }
}
