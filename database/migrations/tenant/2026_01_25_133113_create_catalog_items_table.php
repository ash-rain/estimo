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
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');

            // Pricing
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');

            // Units & Quantities
            $table->string('unit_type')->default('each'); // each, hour, sqft, lbs, etc.
            $table->decimal('minimum_quantity', 10, 2)->default(1);
            $table->boolean('is_taxable')->default(true);

            // Variants Support
            $table->foreignId('parent_id')->nullable()->constrained('catalog_items')->onDelete('cascade');
            $table->boolean('has_variants')->default(false);
            $table->json('variant_attributes')->nullable(); // e.g., {"size": "Large", "color": "Blue"}

            // Stock & Availability
            $table->boolean('track_inventory')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->nullable();

            // Additional Information
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->string('image_url')->nullable();

            // Status & Tracking
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category_id', 'is_active']);
            $table->index(['name', 'sku']);
            $table->index('parent_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
