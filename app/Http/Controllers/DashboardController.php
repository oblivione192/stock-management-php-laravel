<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $totalStockValue = (float) Product::query()
            ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as total')
            ->value('total');

        $metrics = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'suppliers' => Supplier::count(),
            'totalUnits' => (int) Product::sum('current_stock'),
            'lowStockProducts' => Product::whereColumn('current_stock', '<=', 'reorder_level')->count(),
            'totalStockValue' => $totalStockValue,
        ];

        $lowStockProducts = Product::query()
            ->with('category:id,name')
            ->select(['id', 'sku', 'name', 'category_id', 'current_stock', 'reorder_level'])
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->orderBy('current_stock')
            ->limit(10)
            ->get();

        $recentMovements = StockMovement::query()
            ->with([
                'product:id,name,sku',
            ])
            ->select([
                'id',
                'product_id',
                'movement_type',
                'quantity',
                'reference',
                'moved_at',
            ])
            ->latest('moved_at')
            ->limit(10)
            ->get();

        return response()->json([
            'metrics' => $metrics,
            'lowStockProducts' => $lowStockProducts,
            'recentMovements' => $recentMovements,
        ]);
    }
}
