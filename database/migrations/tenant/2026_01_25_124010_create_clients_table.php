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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('US');

            // Financial
            $table->string('currency', 3)->default('USD');
            $table->boolean('tax_exempt')->default(false);
            $table->decimal('tax_rate', 5, 2)->nullable();

            // Additional info
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('active'); // active, inactive, archived

            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_contact_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_name', 'email']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
