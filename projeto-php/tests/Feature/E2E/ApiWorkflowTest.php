<?php

namespace Tests\Feature\E2E;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use DatabaseMigrations;

    public function test_user_can_execute_the_full_api_workflow_and_database_state_can_be_inspected_at_the_end(): void
    {
        $registerResponse = $this->postJson('/api/register', [
            'name' => 'Workflow User',
            'email' => 'workflow@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $registerResponse
            ->assertCreated()
            ->assertJsonPath('data.user.email', 'workflow@example.com');

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'workflow@example.com',
            'password' => 'secret123',
        ]);

        $loginResponse
            ->assertOk()
            ->assertJsonPath('data.user.name', 'Workflow User');

        $token = $loginResponse->json('data.token');
        $headers = $this->authHeaders($token);

        $createCategoryResponse = $this->postJson('/api/categories', [
            'name' => 'Categoria E2E',
            'description' => 'Criada durante o fluxo completo.',
        ], $headers);

        $createCategoryResponse->assertCreated();
        $categoryId = $createCategoryResponse->json('data.id');

        $this->getJson('/api/categories', $headers)
            ->assertOk()
            ->assertJsonPath('data.0.id', $categoryId);

        $this->getJson('/api/categories/'.$categoryId, $headers)
            ->assertOk()
            ->assertJsonPath('data.name', 'Categoria E2E');

        $this->putJson('/api/categories/'.$categoryId, [
            'name' => 'Categoria E2E Atualizada',
            'description' => 'Descrição atualizada.',
        ], $headers)
            ->assertOk()
            ->assertJsonPath('data.name', 'Categoria E2E Atualizada');

        $createProductResponse = $this->postJson('/api/products', [
            'category_id' => $categoryId,
            'name' => 'Produto E2E',
            'description' => 'Produto criado durante o fluxo.',
            'price' => 199.90,
            'available' => true,
        ], $headers);

        $createProductResponse
            ->assertCreated()
            ->assertJsonPath('data.category_id', $categoryId);

        $productId = $createProductResponse->json('data.id');

        $this->getJson('/api/products?category_id='.$categoryId.'&available=1&search=Produto&sort_by=price&sort_order=asc&per_page=15', $headers)
            ->assertOk()
            ->assertJsonPath('data.0.id', $productId)
            ->assertJsonPath('meta.total', 1);

        $this->getJson('/api/products/'.$productId, $headers)
            ->assertOk()
            ->assertJsonPath('data.name', 'Produto E2E');

        $this->putJson('/api/products/'.$productId, [
            'name' => 'Produto E2E Atualizado',
            'description' => 'Produto atualizado no fluxo.',
            'price' => 249.90,
            'available' => false,
        ], $headers)
            ->assertOk()
            ->assertJsonPath('data.name', 'Produto E2E Atualizado')
            ->assertJsonPath('data.available', false);

        $persistedUser = DB::table('users')
            ->select('id', 'name', 'email', 'token_version')
            ->where('email', 'workflow@example.com')
            ->first();

        $persistedCategory = DB::table('categories')
            ->select('id', 'name', 'description')
            ->where('id', $categoryId)
            ->first();

        $persistedProduct = DB::table('products')
            ->select('id', 'category_id', 'name', 'description', 'price', 'available')
            ->where('id', $productId)
            ->first();

        $this->assertNotNull($persistedUser);
        $this->assertNotNull($persistedCategory);
        $this->assertNotNull($persistedProduct);
        $this->assertSame('Workflow User', $persistedUser->name);
        $this->assertSame('Categoria E2E Atualizada', $persistedCategory->name);
        $this->assertSame('Produto E2E Atualizado', $persistedProduct->name);
        $this->assertSame('249.90', (string) $persistedProduct->price);
        $this->assertSame(0, (int) $persistedProduct->available);

        $this->deleteJson('/api/products/'.$productId, [], $headers)
            ->assertNoContent();

        $this->deleteJson('/api/categories/'.$categoryId, [], $headers)
            ->assertNoContent();

        $this->postJson('/api/logout', [], $headers)
            ->assertOk()
            ->assertJson([
                'message' => 'Logout realizado com sucesso.',
            ]);

        $databaseSnapshot = [
            'users' => DB::table('users')
                ->select('name', 'email', 'token_version')
                ->orderBy('id')
                ->get()
                ->map(fn (object $user): array => (array) $user)
                ->all(),
            'categories' => DB::table('categories')
                ->select('name', 'description')
                ->orderBy('id')
                ->get()
                ->map(fn (object $category): array => (array) $category)
                ->all(),
            'products' => DB::table('products')
                ->select('name', 'price', 'available')
                ->orderBy('id')
                ->get()
                ->map(fn (object $product): array => (array) $product)
                ->all(),
        ];

        $this->assertCount(1, $databaseSnapshot['users']);
        $this->assertSame('workflow@example.com', $databaseSnapshot['users'][0]['email']);
        $this->assertSame(1, (int) $databaseSnapshot['users'][0]['token_version']);
        $this->assertSame([], $databaseSnapshot['categories']);
        $this->assertSame([], $databaseSnapshot['products']);
    }
}
