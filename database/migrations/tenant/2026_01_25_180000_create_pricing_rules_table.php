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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('type', ['discount', 'markup', 'fixed_price', 'volume_discount'])->default('discount');
            $table->enum('value_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2); // Discount/markup amount or percentage
            $table->json('conditions')->nullable(); // Rules for when this applies (min_qty, max_qty, client_ids, category_ids, etc.)
            $table->integer('priority')->default(0); // Higher priority rules apply first
            $table->boolean('active')->default(true);
            $table->enum('applies_to', ['all', 'categories', 'items', 'clients'])->default('all');
            $table->timestamps();

            $table->index('active');
            $table->index('priority');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
