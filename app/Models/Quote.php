<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quote_number',
        'portal_token',
        'title',
        'description',
        'client_id',
        'status',
        'sent_at',
        'viewed_at',
        'portal_viewed_at',
        'portal_view_count',
        'accepted_at',
        'rejected_at',
        'quote_date',
        'valid_until',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'total',
        'currency',
        'notes',
        'terms',
        'footer',
        'version',
        'parent_quote_id',
        'created_by',
        'last_calculated_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'portal_viewed_at' => 'datetime',
        'portal_view_count' => 'integer',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'last_calculated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $quote->quote_number = static::generateQuoteNumber();
            }
            if (empty($quote->quote_date)) {
                $quote->quote_date = now()->toDateString();
            }
            if (empty($quote->valid_until)) {
                $quote->valid_until = now()->addDays(30)->toDateString();
            }
        });
    }

    /**
     * Generate a unique quote number.
     */
    public static function generateQuoteNumber(): string
    {
        $year = now()->year;
        $lastQuote = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastQuote ? ((int) substr($lastQuote->quote_number, -4)) + 1 : 1;

        return 'Q-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the client associated with the quote.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who created the quote.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the quote items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('sort_order');
    }

    /**
     * Get the parent quote (for revisions).
     */
    public function parentQuote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'parent_quote_id');
    }

    /**
     * Get the child quotes (revisions).
     */
    public function childQuotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'parent_quote_id');
    }

    /**
     * Get the quote revisions (snapshots).
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(QuoteRevision::class)->orderBy('revision_number', 'desc');
    }

    /**
     * Get the email tracking records for this quote.
     */
    public function emails(): HasMany
    {
        return $this->hasMany(QuoteEmail::class)->orderBy('sent_at', 'desc');
    }

    /**
     * Get the quote acceptance record.
     */
    public function acceptance(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(QuoteAcceptance::class);
    }

    /**
     * Calculate quote totals.
     */
    public function calculate(): void
    {
        // Calculate subtotal from items
        $this->subtotal = $this->items->sum('subtotal');

        // Calculate discount amount
        if ($this->discount_rate > 0) {
            $this->discount_amount = $this->subtotal * ($this->discount_rate / 100);
        }

        $subtotalAfterDiscount = $this->subtotal - $this->discount_amount;

        // Calculate tax amount (only on taxable items)
        if ($this->tax_rate > 0) {
            $taxableAmount = $this->items->where('is_taxable', true)->sum('subtotal');
            $taxableAfterDiscount = $taxableAmount - ($this->discount_amount * ($taxableAmount / max($this->subtotal, 0.01)));
            $this->tax_amount = $taxableAfterDiscount * ($this->tax_rate / 100);
        } else {
            $this->tax_amount = 0;
        }

        // Calculate total
        $this->total = $subtotalAfterDiscount + $this->tax_amount;

        $this->last_calculated_at = now();
    }

    /**
     * Mark the quote as sent.
     */
    public function markAsSent(): void
    {
        if ($this->status === 'draft') {
            $this->status = 'sent';
            $this->sent_at = now();
            $this->save();
        }
    }

    /**
     * Mark the quote as viewed.
     */
    public function markAsViewed(): void
    {
        if (is_null($this->viewed_at)) {
            $this->viewed_at = now();
            if ($this->status === 'sent') {
                $this->status = 'viewed';
            }
            $this->save();
        }
    }

    /**
     * Mark the quote as accepted.
     */
    public function markAsAccepted(): void
    {
        $this->status = 'accepted';
        $this->accepted_at = now();
        $this->save();
    }

    /**
     * Mark the quote as rejected.
     */
    public function markAsRejected(): void
    {
        $this->status = 'rejected';
        $this->rejected_at = now();
        $this->save();
    }

    /**
     * Check if the quote is expired.
     */
    public function isExpired(): bool
    {
        return $this->valid_until && Carbon::parse($this->valid_until)->isPast();
    }

    /**
     * Check if the quote is editable.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'sent']);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by client.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope to search quotes.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('quote_number', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Get formatted total.
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->total, 2);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'sent' => 'Sent',
            'viewed' => 'Viewed',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'viewed' => 'purple',
            'accepted' => 'green',
            'rejected' => 'red',
            'expired' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Generate a unique portal access token.
     */
    public function generatePortalToken(): string
    {
        $token = \Illuminate\Support\Str::random(64);

        // Ensure uniqueness
        while (static::where('portal_token', $token)->exists()) {
            $token = \Illuminate\Support\Str::random(64);
        }

        $this->update(['portal_token' => $token]);

        return $token;
    }

    /**
     * Get the portal URL for this quote.
     */
    public function getPortalUrl(): string
    {
        if (! $this->portal_token) {
            $this->generatePortalToken();
        }

        return route('portal.quote.show', ['token' => $this->portal_token]);
    }

    /**
     * Check if the quote has been accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if the quote has been rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the quote has expired.
     */
    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast() && $this->status !== 'accepted';
    }

    /**
     * Create a revision snapshot of the current quote.
     */
    public function createRevision(?string $notes = null, ?int $userId = null): QuoteRevision
    {
        // Get the next revision number
        $latestRevision = $this->revisions()->orderBy('revision_number', 'desc')->first();
        $revisionNumber = $latestRevision ? $latestRevision->revision_number + 1 : 1;

        // Prepare snapshot data
        $data = [
            'quote_number' => $this->quote_number,
            'title' => $this->title,
            'description' => $this->description,
            'client_id' => $this->client_id,
            'client_name' => $this->client?->name,
            'status' => $this->status,
            'quote_date' => $this->quote_date?->toDateString(),
            'valid_until' => $this->valid_until?->toDateString(),
            'subtotal' => (float) $this->subtotal,
            'tax_rate' => (float) $this->tax_rate,
            'tax_amount' => (float) $this->tax_amount,
            'discount_rate' => (float) $this->discount_rate,
            'discount_amount' => (float) $this->discount_amount,
            'total' => (float) $this->total,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'footer' => $this->footer,
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'catalog_item_id' => $item->catalog_item_id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'quantity' => (float) $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => (float) $item->unit_price,
                    'is_taxable' => $item->is_taxable,
                    'subtotal' => (float) $item->subtotal,
                    'sort_order' => $item->sort_order,
                ];
            })->toArray(),
        ];

        // Create the revision
        return $this->revisions()->create([
            'revision_number' => $revisionNumber,
            'notes' => $notes,
            'data' => $data,
            'created_by' => $userId ?? auth()->id(),
            'parent_revision_id' => $latestRevision?->id,
        ]);
    }

    /**
     * Get the current revision number.
     */
    public function getCurrentRevisionNumber(): int
    {
        $latestRevision = $this->revisions()->orderBy('revision_number', 'desc')->first();

        return $latestRevision ? $latestRevision->revision_number : 0;
    }

    /**
     * Restore quote from a specific revision.
     */
    public function restoreFromRevision(QuoteRevision $revision): void
    {
        $data = $revision->data;

        // Update quote fields (excluding items)
        $this->update([
            'title' => $data['title'] ?? $this->title,
            'description' => $data['description'] ?? $this->description,
            'quote_date' => $data['quote_date'] ?? $this->quote_date,
            'valid_until' => $data['valid_until'] ?? $this->valid_until,
            'tax_rate' => $data['tax_rate'] ?? $this->tax_rate,
            'discount_rate' => $data['discount_rate'] ?? $this->discount_rate,
            'notes' => $data['notes'] ?? $this->notes,
            'terms' => $data['terms'] ?? $this->terms,
            'footer' => $data['footer'] ?? $this->footer,
        ]);

        // We don't restore items directly here - that would be done through UI
        // to allow users to review changes before applying
    }

    /**
     * Get revision version name (e.g., "v3").
     */
    public function getRevisionVersionName(): string
    {
        $number = $this->getCurrentRevisionNumber();

        return $number > 0 ? "v{$number}" : '';
    }
}
