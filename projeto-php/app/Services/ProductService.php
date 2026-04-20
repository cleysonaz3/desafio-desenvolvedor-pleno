<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Product::query()->with('category');

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (array_key_exists('available', $filters)) {
            $query->where('available', $filters['available']);
        }

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);

            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $perPage = $filters['per_page'] ?? 15;

        return $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array{category_id: int, name: string, description?: string|null, image_url?: string|null, price: numeric-string|float|int, available?: bool}  $data
     */
    public function store(array $data): Product
    {
        return Product::query()
            ->create($data)
            ->load('category');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->fill($data);
        $product->save();

        return $product->refresh()->load('category');
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return Collection<int, Product>
     */
    public function import(array $items): Collection
    {
        /** @var Collection<int, Product> $imported */
        $imported = DB::transaction(function () use ($items): Collection {
            $products = collect();
            $categoriesByName = [];

            foreach ($items as $item) {
                $categoryId = $this->resolveCategoryId($item, $categoriesByName);

                $product = Product::query()
                    ->create([
                        'category_id' => $categoryId,
                        'name' => $item['name'],
                        'description' => $item['description'] ?? null,
                        'image_url' => $item['image_url'] ?? null,
                        'price' => $item['price'],
                        'available' => $item['available'] ?? true,
                    ])
                    ->load('category');

                $products->push($product);
            }

            return $products;
        });

        return $imported;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, int>  $categoriesByName
     */
    private function resolveCategoryId(array $item, array &$categoriesByName): int
    {
        if (isset($item['category_id'])) {
            return (int) $item['category_id'];
        }

        $categoryName = trim((string) ($item['category_name'] ?? ''));
        $categoryKey = mb_strtolower($categoryName);

        if (isset($categoriesByName[$categoryKey])) {
            return $categoriesByName[$categoryKey];
        }

        $category = Category::query()->firstOrCreate(
            ['name' => $categoryName],
            ['description' => null]
        );

        $categoriesByName[$categoryKey] = (int) $category->getKey();

        return (int) $category->getKey();
    }
}
