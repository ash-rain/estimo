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
        Schema::create('quote_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->string('client_name');
            $table->string('client_email');
            $table->enum('action', ['accepted', 'rejected']);
            $table->text('signature_data')->nullable(); // Base64 encoded signature image
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['quote_id', 'action']);
            $table->index('accepted_at');
        });

        // Add portal_token to quotes table
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('portal_token')->unique()->nullable()->after('quote_number');
            $table->timestamp('portal_viewed_at')->nullable()->after('viewed_at');
            $table->integer('portal_view_count')->default(0)->after('portal_viewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['portal_token', 'portal_viewed_at', 'portal_view_count']);
        });

        Schema::dropIfExists('quote_acceptances');
    }
};
