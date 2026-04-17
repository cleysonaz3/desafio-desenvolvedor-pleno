<?php

namespace Tests\Feature\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseMigrations;

    public function test_authenticated_user_can_create_and_view_a_product(): void
    {
        $headers = $this->authHeaders();
        $category = Category::factory()->create();

        $createResponse = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Whey Protein',
            'description' => 'Proteína concentrada.',
            'price' => 149.90,
            'available' => true,
        ], $headers);

        $productId = $createResponse->json('data.id');

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.category.id', $category->id);

        $this->getJson('/api/products/'.$productId, $headers)
            ->assertOk()
            ->assertJsonPath('data.name', 'Whey Protein');
    }

    public function test_products_can_be_filtered_sorted_and_paginated(): void
    {
        $headers = $this->authHeaders();
        $targetCategory = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $targetCategory->id,
            'name' => 'Albumina',
            'price' => 89.90,
            'available' => true,
        ]);

        Product::factory()->create([
            'category_id' => $targetCategory->id,
            'name' => 'Creatina',
            'price' => 129.90,
            'available' => true,
        ]);

        Product::factory()->create([
            'category_id' => $otherCategory->id,
            'name' => 'Colageno',
            'price' => 59.90,
            'available' => false,
        ]);

        $this->getJson('/api/products?category_id='.$targetCategory->id.'&available=1&sort_by=price&sort_order=asc&per_page=1', $headers)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Albumina')
            ->assertJsonPath('meta.total', 2);
    }

    public function test_product_validation_rejects_negative_prices(): void
    {
        $headers = $this->authHeaders();
        $category = Category::factory()->create();

        $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Produto Invalido',
            'price' => -10,
        ], $headers)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price']);
    }
}
