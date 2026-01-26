<?php

namespace App\Livewire\Terms;

use App\Models\TermsLibrary;
use Livewire\Component;
use Livewire\WithPagination;

class TermsLibraryList extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $showModal = false;
    public $editingTermId = null;

    public $title = '';
    public $content = '';
    public $termCategory = '';
    public $is_default = false;
    public $order = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['title', 'content', 'termCategory', 'is_default', 'order', 'editingTermId']);
        $this->showModal = true;
    }

    public function openEditModal($termId)
    {
        $term = TermsLibrary::findOrFail($termId);

        $this->editingTermId = $term->id;
        $this->title = $term->title;
        $this->content = $term->content;
        $this->termCategory = $term->category ?? '';
        $this->is_default = $term->is_default;
        $this->order = $term->order;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'content', 'termCategory', 'is_default', 'order', 'editingTermId']);
    }

    public function saveTerm()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'termCategory' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        if ($this->editingTermId) {
            $term = TermsLibrary::findOrFail($this->editingTermId);
            $term->update([
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->termCategory ?: null,
                'is_default' => $this->is_default,
                'order' => $this->order,
            ]);
            $message = 'Terms updated successfully.';
        } else {
            TermsLibrary::create([
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->termCategory ?: null,
                'is_default' => $this->is_default,
                'order' => $this->order,
                'created_by' => auth()->id(),
            ]);
            $message = 'Terms created successfully.';
        }

        $this->dispatch('notify', type: 'success', message: $message);
        $this->closeModal();
    }

    public function deleteTerm($termId)
    {
        $term = TermsLibrary::findOrFail($termId);
        $term->delete();

        $this->dispatch('notify', type: 'success', message: 'Terms deleted successfully.');
    }

    public function duplicateTerm($termId)
    {
        $term = TermsLibrary::findOrFail($termId);
        $term->duplicate();

        $this->dispatch('notify', type: 'success', message: 'Terms duplicated successfully.');
    }

    public function render()
    {
        $query = TermsLibrary::query()->with('creator');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->byCategory($this->category);
        }

        $terms = $query->ordered()->paginate(15);

        $categories = TermsLibrary::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('livewire.terms.terms-library-list', [
            'terms' => $terms,
            'categories' => $categories,
        ]);
    }
}
