<?php

namespace Tests\Feature\Categories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function test_authenticated_user_can_crud_categories(): void
    {
        $headers = $this->authHeaders();

        $createResponse = $this->postJson('/api/categories', [
            'name' => 'Vitaminas',
            'description' => 'Linha de vitaminas.',
        ], $headers);

        $categoryId = $createResponse->json('data.id');

        $createResponse->assertCreated();

        $this->getJson('/api/categories', $headers)
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->putJson('/api/categories/'.$categoryId, [
            'name' => 'Vitaminas Premium',
        ], $headers)
            ->assertOk()
            ->assertJsonPath('data.name', 'Vitaminas Premium');

        $this->deleteJson('/api/categories/'.$categoryId, [], $headers)
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', [
            'id' => $categoryId,
        ]);
    }

    public function test_category_validation_errors_are_returned_as_json(): void
    {
        $this->postJson('/api/categories', [], $this->authHeaders())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_show_returns_not_found_for_missing_category(): void
    {
        $this->getJson('/api/categories/999', $this->authHeaders())
            ->assertNotFound()
            ->assertJson([
                'message' => 'Recurso não encontrado.',
            ]);
    }
}
