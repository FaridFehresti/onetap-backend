<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('my_card_links', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('card_id')->constrained('my_cards')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_card_links');
    }
};
