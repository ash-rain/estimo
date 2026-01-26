<?php

namespace App\Livewire\Templates;

use App\Models\Quote;
use App\Models\QuoteTemplate;
use Livewire\Component;

class ApplyTemplate extends Component
{
    public Quote $quote;
    public $showModal = false;
    public $selectedTemplateId = null;
    public $templates;
    public $previewData = null;

    protected $listeners = ['open-apply-template-modal' => 'openModal'];

    public function mount(Quote $quote)
    {
        $this->quote = $quote;
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $this->templates = QuoteTemplate::query()
            ->orderByRaw('is_industry_preset DESC, is_default DESC, name ASC')
            ->get();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->loadTemplates();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['selectedTemplateId', 'previewData']);
    }

    public function selectTemplate($templateId)
    {
        $this->selectedTemplateId = $templateId;
        
        $template = QuoteTemplate::find($templateId);
        if ($template) {
            $this->previewData = $template->preview();
        }
    }

    public function applyTemplate()
    {
        if (!$this->selectedTemplateId) {
            $this->dispatch('notify', type: 'error', message: 'Please select a template.');
            return;
        }

        $template = QuoteTemplate::findOrFail($this->selectedTemplateId);
        
        $this->quote->applyTemplate($template);

        $this->dispatch('notify', type: 'success', message: 'Template applied successfully.');
        $this->dispatch('template-applied');
        $this->dispatch('refresh-quote');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.templates.apply-template');
    }
}
