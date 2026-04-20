<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->index(['category_id', 'available'], 'products_category_available_index');
            $table->index('price', 'products_price_index');
            $table->index('created_at', 'products_created_at_index');
            $table->index('name', 'products_name_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->dropIndex('products_category_available_index');
            $table->dropIndex('products_price_index');
            $table->dropIndex('products_created_at_index');
            $table->dropIndex('products_name_index');
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }
};
