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
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();

            // Quote Relationship
            $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade');

            // Catalog Item Reference (optional)
            $table->foreignId('catalog_item_id')->nullable()->constrained('catalog_items')->onDelete('set null');

            // Item Details (snapshot at time of quote)
            $table->string('name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();

            // Pricing & Quantities
            $table->decimal('quantity', 10, 2);
            $table->string('unit_type')->default('each');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_rate', 5, 2)->default(0); // Percentage
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->boolean('is_taxable')->default(true);

            // Optional Fields
            $table->json('metadata')->nullable(); // For custom fields
            $table->text('notes')->nullable();

            // Ordering
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['quote_id', 'sort_order']);
            $table->index('catalog_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
