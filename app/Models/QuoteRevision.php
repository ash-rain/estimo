<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'revision_number',
        'notes',
        'data',
        'created_by',
        'parent_revision_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the quote that owns the revision.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the user who created the revision.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parent revision.
     */
    public function parentRevision(): BelongsTo
    {
        return $this->belongsTo(QuoteRevision::class, 'parent_revision_id');
    }

    /**
     * Get revision display name (e.g., "v2", "v3").
     */
    public function getVersionNameAttribute(): string
    {
        return 'v' . $this->revision_number;
    }

    /**
     * Get the items from the snapshot data.
     */
    public function getItemsAttribute(): array
    {
        return $this->data['items'] ?? [];
    }

    /**
     * Get the totals from the snapshot data.
     */
    public function getTotalsAttribute(): array
    {
        return [
            'subtotal' => $this->data['subtotal'] ?? 0,
            'tax' => $this->data['tax'] ?? 0,
            'discount' => $this->data['discount'] ?? 0,
            'total' => $this->data['total'] ?? 0,
        ];
    }

    /**
     * Compare this revision with another revision.
     */
    public function compareWith(QuoteRevision $other): array
    {
        $changes = [];

        // Compare totals
        if ($this->totals['total'] !== $other->totals['total']) {
            $changes['total'] = [
                'from' => $other->totals['total'],
                'to' => $this->totals['total'],
                'diff' => $this->totals['total'] - $other->totals['total'],
            ];
        }

        // Compare item counts
        $thisItemCount = count($this->items);
        $otherItemCount = count($other->items);
        if ($thisItemCount !== $otherItemCount) {
            $changes['item_count'] = [
                'from' => $otherItemCount,
                'to' => $thisItemCount,
                'diff' => $thisItemCount - $otherItemCount,
            ];
        }

        // Compare individual items
        $itemChanges = $this->compareItems($this->items, $other->items);
        if (! empty($itemChanges)) {
            $changes['items'] = $itemChanges;
        }

        return $changes;
    }

    /**
     * Compare two sets of items.
     */
    protected function compareItems(array $currentItems, array $previousItems): array
    {
        $changes = [
            'added' => [],
            'removed' => [],
            'modified' => [],
        ];

        $currentIds = array_column($currentItems, 'id');
        $previousIds = array_column($previousItems, 'id');

        // Find added items
        foreach ($currentItems as $item) {
            if (! in_array($item['id'], $previousIds)) {
                $changes['added'][] = $item;
            }
        }

        // Find removed items
        foreach ($previousItems as $item) {
            if (! in_array($item['id'], $currentIds)) {
                $changes['removed'][] = $item;
            }
        }

        // Find modified items
        foreach ($currentItems as $currentItem) {
            foreach ($previousItems as $previousItem) {
                if ($currentItem['id'] === $previousItem['id']) {
                    $itemChanges = [];

                    if ($currentItem['quantity'] !== $previousItem['quantity']) {
                        $itemChanges['quantity'] = [
                            'from' => $previousItem['quantity'],
                            'to' => $currentItem['quantity'],
                        ];
                    }

                    if ($currentItem['unit_price'] !== $previousItem['unit_price']) {
                        $itemChanges['unit_price'] = [
                            'from' => $previousItem['unit_price'],
                            'to' => $currentItem['unit_price'],
                        ];
                    }

                    if (! empty($itemChanges)) {
                        $changes['modified'][] = [
                            'item' => $currentItem,
                            'changes' => $itemChanges,
                        ];
                    }
                }
            }
        }

        return $changes;
    }

    /**
     * Get a formatted summary of what changed.
     */
    public function getChangeSummary(QuoteRevision $previous): string
    {
        $changes = $this->compareWith($previous);
        $summary = [];

        if (isset($changes['total'])) {
            $diff = $changes['total']['diff'];
            $summary[] = sprintf(
                'Total %s by %s',
                $diff > 0 ? 'increased' : 'decreased',
                '$' . number_format(abs($diff), 2)
            );
        }

        if (isset($changes['items']['added']) && count($changes['items']['added']) > 0) {
            $summary[] = count($changes['items']['added']) . ' item(s) added';
        }

        if (isset($changes['items']['removed']) && count($changes['items']['removed']) > 0) {
            $summary[] = count($changes['items']['removed']) . ' item(s) removed';
        }

        if (isset($changes['items']['modified']) && count($changes['items']['modified']) > 0) {
            $summary[] = count($changes['items']['modified']) . ' item(s) modified';
        }

        return empty($summary) ? 'No changes' : implode(', ', $summary);
    }
}
