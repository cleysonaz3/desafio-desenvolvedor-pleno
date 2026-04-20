<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ImportProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'items.*.category_name' => ['nullable', 'string', 'max:255'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.image_url' => ['nullable', 'url', 'max:2048'],
            'items.*.price' => ['required', 'numeric', 'gt:0'],
            'items.*.available' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $hasCategoryId = isset($item['category_id']) && $item['category_id'] !== '' && $item['category_id'] !== null;
                $hasCategoryName = isset($item['category_name']) && trim((string) $item['category_name']) !== '';

                if (! $hasCategoryId && ! $hasCategoryName) {
                    $validator->errors()->add(
                        "items.$index.category_id",
                        'Informe category_id ou category_name para cada item.'
                    );
                }
            }
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function items(): array
    {
        /** @var array<int, array<string, mixed>> $items */
        $items = $this->validated('items', []);

        return array_map(static function (array $item): array {
            $normalized = [
                'name' => trim((string) $item['name']),
                'description' => $item['description'] ?? null,
                'image_url' => $item['image_url'] ?? null,
                'price' => (float) $item['price'],
                'available' => array_key_exists('available', $item)
                    ? filter_var($item['available'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
                    : true,
            ];

            if (isset($item['category_id']) && $item['category_id'] !== '' && $item['category_id'] !== null) {
                $normalized['category_id'] = (int) $item['category_id'];
            }

            if (isset($item['category_name']) && trim((string) $item['category_name']) !== '') {
                $normalized['category_name'] = trim((string) $item['category_name']);
            }

            return $normalized;
        }, $items);
    }
}
