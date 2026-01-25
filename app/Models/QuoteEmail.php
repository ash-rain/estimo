<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QuoteEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'recipient_email',
        'recipient_name',
        'message',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'open_count',
        'click_count',
        'tracking_token',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'open_count' => 'integer',
        'click_count' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quoteEmail) {
            if (!$quoteEmail->tracking_token) {
                $quoteEmail->tracking_token = Str::random(32);
            }
            if (!$quoteEmail->sent_at) {
                $quoteEmail->sent_at = now();
            }
        });
    }

    /**
     * Get the quote that owns the email.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Mark the email as opened.
     */
    public function markAsOpened(): void
    {
        $this->increment('open_count');

        if (!$this->opened_at) {
            $this->update([
                'opened_at' => now(),
                'status' => 'opened',
            ]);
        }
    }

    /**
     * Mark the email as clicked.
     */
    public function markAsClicked(): void
    {
        $this->increment('click_count');

        if (!$this->clicked_at) {
            $this->update([
                'clicked_at' => now(),
                'status' => 'clicked',
            ]);
        }
    }

    /**
     * Mark the email as delivered.
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'delivered_at' => now(),
            'status' => 'delivered',
        ]);
    }

    /**
     * Mark the email as bounced.
     */
    public function markAsBounced(string $error = null): void
    {
        $this->update([
            'status' => 'bounced',
            'error_message' => $error,
        ]);
    }

    /**
     * Mark the email as failed.
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
    }

    /**
     * Check if the email was opened.
     */
    public function wasOpened(): bool
    {
        return $this->opened_at !== null;
    }

    /**
     * Check if the email was clicked.
     */
    public function wasClicked(): bool
    {
        return $this->clicked_at !== null;
    }

    /**
     * Get the tracking URL for opens.
     */
    public function getOpenTrackingUrl(): string
    {
        return route('tracking.email.open', ['token' => $this->tracking_token]);
    }

    /**
     * Get the tracking URL for clicks.
     */
    public function getClickTrackingUrl(): string
    {
        return route('tracking.email.click', ['token' => $this->tracking_token]);
    }
}
