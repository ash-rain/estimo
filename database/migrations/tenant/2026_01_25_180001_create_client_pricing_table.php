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
        Schema::create('client_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('catalog_item_id')->constrained()->onDelete('cascade');
            $table->decimal('custom_price', 10, 2);
            $table->enum('price_type', ['fixed', 'discount_percentage', 'markup_percentage'])->default('fixed');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Unique constraint: one pricing rule per client-item combination
            $table->unique(['client_id', 'catalog_item_id']);
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_pricing');
    }
};
