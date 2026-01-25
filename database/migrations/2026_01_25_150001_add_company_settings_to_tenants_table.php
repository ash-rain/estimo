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
        Schema::table('tenants', function (Blueprint $table) {
            // Company Information (some columns may already exist)
            if (!Schema::hasColumn('tenants', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('tenants', 'website')) {
                $table->string('website')->nullable()->after('phone');
            }

            // Address
            $table->string('address')->nullable()->after('website');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');

            // Tax & Business Details
            $table->string('tax_id')->nullable()->after('country');
            $table->string('registration_number')->nullable()->after('tax_id');

            // Branding
            $table->string('logo_url')->nullable()->after('registration_number');
            $table->string('primary_color')->default('#4F46E5')->after('logo_url');
            $table->string('secondary_color')->default('#10B981')->after('primary_color');

            // Quote Settings
            $table->string('default_currency')->default('$')->after('secondary_color');
            $table->decimal('default_tax_rate', 5, 2)->default(0)->after('default_currency');
            $table->integer('quote_validity_days')->default(30)->after('default_tax_rate');

            // Email Settings
            $table->string('quote_email_subject')->nullable()->after('quote_validity_days');
            $table->text('quote_email_message')->nullable()->after('quote_email_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'website',
                'address',
                'city',
                'state',
                'postal_code',
                'country',
                'tax_id',
                'registration_number',
                'logo_url',
                'primary_color',
                'secondary_color',
                'default_currency',
                'default_tax_rate',
                'quote_validity_days',
                'quote_email_subject',
                'quote_email_message',
            ]);
        });
    }
};
