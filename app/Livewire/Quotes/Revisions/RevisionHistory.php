<?php

namespace App\Livewire\Quotes\Revisions;

use App\Models\Quote;
use App\Models\QuoteRevision;
use Livewire\Component;

class RevisionHistory extends Component
{
    public Quote $quote;

    public bool $showModal = false;

    public ?int $selectedRevisionId = null;

    public ?int $compareRevisionId = null;

    protected $listeners = ['refresh-revisions' => '$refresh'];

    /**
     * Open the modal.
     */
    public function openModal(): void
    {
        $this->showModal = true;
    }

    /**
     * Close the modal.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedRevisionId = null;
        $this->compareRevisionId = null;
    }

    /**
     * Select a revision to view details.
     */
    public function selectRevision(int $revisionId): void
    {
        $this->selectedRevisionId = $revisionId;
        $this->compareRevisionId = null;
    }

    /**
     * Set revision for comparison.
     */
    public function setCompareRevision(int $revisionId): void
    {
        $this->compareRevisionId = $revisionId;
    }

    /**
     * Restore a specific revision.
     */
    public function restoreRevision(int $revisionId): void
    {
        try {
            $revision = QuoteRevision::findOrFail($revisionId);

            // Check if user has permission
            if ($revision->quote_id !== $this->quote->id) {
                throw new \Exception('Invalid revision.');
            }

            $this->quote->restoreFromRevision($revision);
            $this->quote->refresh();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Quote restored from ' . $revision->version_name . ' successfully!',
            ]);

            $this->dispatch('quote-restored');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to restore revision: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the selected revision.
     */
    public function getSelectedRevisionProperty(): ?QuoteRevision
    {
        return $this->selectedRevisionId
            ? QuoteRevision::find($this->selectedRevisionId)
            : null;
    }

    /**
     * Get the comparison revision.
     */
    public function getCompareRevisionProperty(): ?QuoteRevision
    {
        return $this->compareRevisionId
            ? QuoteRevision::find($this->compareRevisionId)
            : null;
    }

    public function render()
    {
        $revisions = $this->quote->revisions()
            ->with('creator')
            ->orderBy('revision_number', 'desc')
            ->get();

        return view('livewire.quotes.revisions.revision-history', [
            'revisions' => $revisions,
        ]);
    }
}
