<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteAcceptance extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'client_name',
        'client_email',
        'action',
        'signature_data',
        'ip_address',
        'user_agent',
        'rejection_reason',
        'notes',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($acceptance) {
            if ($acceptance->action === 'accepted' && !$acceptance->accepted_at) {
                $acceptance->accepted_at = now();
            } elseif ($acceptance->action === 'rejected' && !$acceptance->rejected_at) {
                $acceptance->rejected_at = now();
            }
        });
    }

    /**
     * Get the quote that owns the acceptance.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Check if this is an acceptance.
     */
    public function isAcceptance(): bool
    {
        return $this->action === 'accepted';
    }

    /**
     * Check if this is a rejection.
     */
    public function isRejection(): bool
    {
        return $this->action === 'rejected';
    }

    /**
     * Get formatted timestamp.
     */
    public function getTimestamp(): ?\Carbon\Carbon
    {
        return $this->action === 'accepted' ? $this->accepted_at : $this->rejected_at;
    }
}
