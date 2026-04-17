<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'gte:min_price'],
            'available' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'in:name,price,created_at'],
            'sort_order' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        $filters = $this->validated();

        if (isset($filters['category_id'])) {
            $filters['category_id'] = (int) $filters['category_id'];
        }

        if (isset($filters['min_price'])) {
            $filters['min_price'] = (float) $filters['min_price'];
        }

        if (isset($filters['max_price'])) {
            $filters['max_price'] = (float) $filters['max_price'];
        }

        if (array_key_exists('available', $filters)) {
            $filters['available'] = filter_var($filters['available'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if (isset($filters['per_page'])) {
            $filters['per_page'] = (int) $filters['per_page'];
        }

        return $filters;
    }
}
