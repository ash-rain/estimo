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
        Schema::create('quote_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->integer('revision_number')->default(1);
            $table->text('notes')->nullable(); // Why this revision was created
            $table->json('data'); // Snapshot of quote data at this revision
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('parent_revision_id')->nullable()->constrained('quote_revisions')->onDelete('set null');
            $table->timestamps();

            // Index for performance
            $table->index(['quote_id', 'revision_number']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_revisions');
    }
};
