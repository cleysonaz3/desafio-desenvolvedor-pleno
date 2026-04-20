<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('showcase_tone', 80)->nullable()->after('image_url');
            $table->string('showcase_caption', 255)->nullable()->after('showcase_tone');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['showcase_tone', 'showcase_caption']);
        });
    }
};
