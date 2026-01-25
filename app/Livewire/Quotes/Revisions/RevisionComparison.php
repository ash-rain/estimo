<?php

namespace App\Livewire\Quotes\Revisions;

use App\Models\QuoteRevision;
use Livewire\Component;

class RevisionComparison extends Component
{
    public QuoteRevision $currentRevision;

    public QuoteRevision $previousRevision;

    public bool $showModal = false;

    public array $changes = [];

    public function mount(QuoteRevision $currentRevision, QuoteRevision $previousRevision)
    {
        $this->currentRevision = $currentRevision;
        $this->previousRevision = $previousRevision;
        $this->calculateChanges();
    }

    /**
     * Calculate the differences between revisions.
     */
    public function calculateChanges(): void
    {
        $this->changes = $this->currentRevision->compareWith($this->previousRevision);
    }

    /**
     * Open the comparison modal.
     */
    public function openModal(): void
    {
        $this->showModal = true;
    }

    /**
     * Close the comparison modal.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /**
     * Check if there are any changes.
     */
    public function hasChanges(): bool
    {
        return ! empty($this->changes);
    }

    public function render()
    {
        return view('livewire.quotes.revisions.revision-comparison');
    }
}
