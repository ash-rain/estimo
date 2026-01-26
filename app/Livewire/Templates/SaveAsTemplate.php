<?php

namespace App\Livewire\Templates;

use App\Models\Quote;
use App\Models\QuoteTemplate;
use Livewire\Component;

class SaveAsTemplate extends Component
{
    public Quote $quote;
    public $showModal = false;

    public $name = '';
    public $description = '';
    public $category = '';
    public $is_default = false;
    public $valid_until_days = 30;

    protected $listeners = ['open-save-template-modal' => 'openModal'];

    public function mount(Quote $quote)
    {
        $this->quote = $quote;
        $this->name = $this->quote->title . ' Template';
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'description', 'category', 'is_default', 'valid_until_days']);
    }

    public function saveTemplate()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'valid_until_days' => 'required|integer|min:1|max:365',
        ]);

        $template = $this->quote->saveAsTemplate([
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'is_default' => $this->is_default,
            'valid_until_days' => $this->valid_until_days,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Template created successfully.');
        $this->dispatch('template-created', templateId: $template->id);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.templates.save-as-template');
    }
}
