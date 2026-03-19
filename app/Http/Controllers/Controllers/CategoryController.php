<?php

namespace App\Http\Controllers\Controllers;

use App\Filters\CategoryFilter;
use App\Models\Category;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $categoryFilter = CategoryFilter::createFromRequest($request);
        $categoryQuery = Category::query();

        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);

        if ($categoryFilter->name) {
            $categoryQuery = $categoryQuery->where('name', 'like', '%'.$categoryFilter->name.'%');
        }

        if ($categoryFilter->description) {
            $categoryQuery = $categoryQuery->where('description', 'like', '%'.$categoryFilter->description.'%');
        }

        $categories = $categoryQuery
            ->forPage($page, $limit)
            ->get();

        return new JsonResponse([
            'data' => $categories,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $category = Category::query()->create([
            'name' => $request->name,
            'description' => $request->description,
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),
        ]);

        return new JsonResponse(
            $category,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): JsonResponse
    {
        $categoryId = $request->route('id');
        $category = Category::query()->findOrFail($categoryId);

        if (! $category) {
            return new JsonResponse([
                'error' => 'Category not found',
            ], 404);
        }

        return new JsonResponse([
            $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $categoryId = $request->route('id');
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        try {
            Category::query()->findOrFail($categoryId);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => 'Category not found',
            ], 404);
        }

        $result = Category::query()
            ->where('id', $categoryId)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

        return new JsonResponse(
            $result
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        $categoryId = $request->route('id');
        try {
            $category = Category::query()->findOrFail($categoryId);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => 'Category not found',
            ], 404);
        }

        $result = $category->delete();
        return new JsonResponse(null, 204);

    }
}
