<?php

namespace App\Http\Controllers;

use App\Filters\StockMovementFilter;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class StockMovementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stockMovementFilter = StockMovementFilter::createFromRequest($request);

        $stockMovementQuery = StockMovement::query();

        $stockMovementQuery = $stockMovementQuery->with('product:id,name');

        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);

        if ($stockMovementFilter->stockMovementType) {
            $stockMovementQuery = $stockMovementQuery->where('movement_type', $stockMovementFilter->stockMovementType);
        }

        if ($stockMovementFilter->movedAt) {
            $stockMovementQuery = $stockMovementQuery->where('moved_at', '=', $stockMovementFilter->movedAt);
        }

        if ($stockMovementFilter->productName) {
            $stockMovementQuery = $stockMovementQuery->whereHas('product', function ($q) use ($stockMovementFilter) {
                $q->where('name', 'like', '%'.$stockMovementFilter->productName.'%');
            });
        }

        if ($stockMovementFilter->productSku) {
            $stockMovementQuery = $stockMovementQuery->whereHas('product', function ($q) use ($stockMovementFilter) {
                $q->where('sku', 'like', '%'.$stockMovementFilter->productSku.'%');
            });
        }

        $movements = $stockMovementQuery->paginate(
            perPage: $limit, 
            columns: ['*'], 
             page: $page, 
            pageName: 'page'
        );
        

        return response()->json([
            'movements' => $movements,
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $id = $request->route('id');
        $stockMovement = StockMovement::query()->find($id);

        if (! $stockMovement) {
            return new JsonResponse(['error' => 'Stock movement not found'], 404);
        }

        return new JsonResponse($stockMovement);
    }

    /**
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'movement_type' => ['required', 'string'],
                'quantity' => ['required', 'integer'],
                'reference' => ['nullable', 'string'],
                'notes' => ['nullable', 'string'],
                'moved_at' => ['nullable', 'date'],
            ]);

            $stockMovement = StockMovement::query()->create($validatedData);

            $product = Product::query()->findOrFail($validatedData['product_id']);

            $currentProductStock = (int) $product->current_stock;
            $quantity = (int) $stockMovement->quantity;

            if ($stockMovement->movement_type === StockMovement::TYPE_STOCK_IN) {
                if ($quantity < 0) {
                    throw new RuntimeException('Quantity cannot be negative for stock in');
                }

                $currentProductStock += $quantity;
            } elseif ($stockMovement->movement_type === StockMovement::TYPE_STOCK_OUT) {

                if ($quantity < 0) {
                    throw new RuntimeException('Quantity cannot be negative for stock out');
                }

                if ($currentProductStock < $quantity) {
                    throw new RuntimeException('Insufficient stock.');
                }

                $currentProductStock -= $quantity;
            } elseif ($stockMovement->movement_type === StockMovement::TYPE_ADJUSTMENT) {
                $newStock = $currentProductStock + $quantity;

                if ($newStock < 0) {
                    throw new RuntimeException('Adjustment would make stock negative.');
                }

                $currentProductStock = $newStock;
            }

            $product->current_stock = $currentProductStock;
            $product->save();

            DB::commit();

            return new JsonResponse($stockMovement, 201);

        } catch (Throwable $e) {
            DB::rollBack();

            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
