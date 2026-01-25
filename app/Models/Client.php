<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'currency',
        'tax_exempt',
        'tax_rate',
        'notes',
        'tags',
        'status',
        'created_by',
        'last_contact_at',
    ];

    protected $casts = [
        'tax_exempt' => 'boolean',
        'tax_rate' => 'decimal:2',
        'tags' => 'array',
        'last_contact_at' => 'datetime',
    ];

    /**
     * Get the user who created this client
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all quotes for this client
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Get the full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if client is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Archive the client
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    /**
     * Activate the client
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Update last contact timestamp
     */
    public function touchLastContact(): void
    {
        $this->update(['last_contact_at' => now()]);
    }

    /**
     * Scope to only active clients
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to search clients
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('company_name', 'like', "%{$search}%")
                ->orWhere('contact_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
