<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function list(): Collection
    {
        return Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array{name: string, description?: string|null}  $data
     */
    public function store(array $data): Category
    {
        return Category::query()->create($data);
    }

    /**
     * @param  array{name?: string, description?: string|null}  $data
     */
    public function update(Category $category, array $data): Category
    {
        $category->fill($data);
        $category->save();

        return $category->refresh()->loadCount('products');
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
