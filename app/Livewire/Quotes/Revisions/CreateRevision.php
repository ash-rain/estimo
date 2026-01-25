<?php

namespace App\Livewire\Quotes\Revisions;

use App\Models\Quote;
use Livewire\Component;

class CreateRevision extends Component
{
    public Quote $quote;

    public string $notes = '';

    public bool $showModal = false;

    /**
     * Open the modal.
     */
    public function openModal(): void
    {
        $this->showModal = true;
        $this->notes = '';
    }

    /**
     * Close the modal.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->notes = '';
    }

    /**
     * Create a new revision.
     */
    public function createRevision(): void
    {
        $this->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $revision = $this->quote->createRevision($this->notes, auth()->id());

            $this->dispatch('revision-created', revisionId: $revision->id);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Revision '.$revision->version_name.' created successfully!',
            ]);

            $this->closeModal();

            // Refresh the parent component to show new revision
            $this->dispatch('refresh-revisions');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to create revision: '.$e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.quotes.revisions.create-revision');
    }
}
