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
            'image_url' => 'https://example.com/whey.jpg',
            'price' => 149.90,
            'available' => true,
        ], $headers);

        $productId = $createResponse->json('data.id');

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonPath('data.image_url', 'https://example.com/whey.jpg');

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

    public function test_product_validation_rejects_invalid_image_urls(): void
    {
        $headers = $this->authHeaders();
        $category = Category::factory()->create();

        $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Produto com imagem inválida',
            'price' => 99.90,
            'image_url' => 'nao-e-url',
        ], $headers)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['image_url']);
    }

    public function test_products_can_be_imported_in_batch_using_existing_and_new_categories(): void
    {
        $headers = $this->authHeaders();
        $existingCategory = Category::factory()->create([
            'name' => 'Performance',
        ]);

        $response = $this->postJson('/api/products/import', [
            'items' => [
                [
                    'category_id' => $existingCategory->id,
                    'name' => 'Creatina Growth',
                    'description' => 'Creatina monohidratada.',
                    'image_url' => 'https://example.com/creatina.png',
                    'price' => 119.90,
                    'available' => true,
                ],
                [
                    'category_name' => 'Vitaminas',
                    'name' => 'Multivitamínico A-Z',
                    'description' => 'Suporte diário.',
                    'image_url' => 'https://example.com/multivitaminico.png',
                    'price' => 89.90,
                    'available' => true,
                ],
            ],
        ], $headers);

        $response
            ->assertCreated()
            ->assertJsonPath('imported_count', 2)
            ->assertJsonPath('data.0.name', 'Creatina Growth')
            ->assertJsonPath('data.1.name', 'Multivitamínico A-Z');

        $this->assertDatabaseHas('categories', ['name' => 'Vitaminas']);
        $this->assertDatabaseHas('products', ['name' => 'Creatina Growth']);
        $this->assertDatabaseHas('products', ['name' => 'Multivitamínico A-Z']);
    }

    public function test_import_requires_category_id_or_category_name_for_each_item(): void
    {
        $headers = $this->authHeaders();

        $this->postJson('/api/products/import', [
            'items' => [
                [
                    'name' => 'Produto sem categoria',
                    'price' => 59.90,
                ],
            ],
        ], $headers)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.category_id']);
    }
}
