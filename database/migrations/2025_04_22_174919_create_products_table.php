<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->json('colors')->nullable();
            $table->decimal('product_price', 10, 2)->nullable();
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->cascadeOnDelete();
            $table->integer('template_id')->nullable();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->cascadeOnDelete();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
