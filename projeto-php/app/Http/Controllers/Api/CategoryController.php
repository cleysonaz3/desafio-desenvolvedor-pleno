<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            $this->categoryService->list()
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        return (new CategoryResource(
            $this->categoryService->store($request->validated())
        ))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource(
            $category->loadCount('products')
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        return new CategoryResource(
            $this->categoryService->update($category, $request->validated())
        );
    }

    public function destroy(Category $category): Response
    {
        $this->categoryService->delete($category);

        return response()->noContent();
    }
}
