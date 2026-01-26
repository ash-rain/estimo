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
        Schema::create('quote_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // e.g., 'services', 'products', 'consulting'
            $table->boolean('is_default')->default(false);
            $table->boolean('is_industry_preset')->default(false);
            $table->json('template_data')->nullable(); // Complete quote structure
            $table->json('sections')->nullable(); // Optional section templates
            $table->text('terms_conditions')->nullable();
            $table->text('email_template')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('category');
            $table->index('is_default');
            $table->index('is_industry_preset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_templates');
    }
};
