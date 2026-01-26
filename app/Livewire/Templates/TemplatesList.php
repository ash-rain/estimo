<?php

namespace App\Livewire\Templates;

use App\Models\QuoteTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class TemplatesList extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $showIndustryPresets = true;
    public $showUserTemplates = true;

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

    public function deleteTemplate($templateId)
    {
        $template = QuoteTemplate::findOrFail($templateId);

        // Don't allow deletion of industry presets
        if ($template->is_industry_preset) {
            $this->dispatch('notify', type: 'error', message: 'Industry presets cannot be deleted.');
            return;
        }

        $template->delete();

        $this->dispatch('notify', type: 'success', message: 'Template deleted successfully.');
    }

    public function duplicateTemplate($templateId)
    {
        $template = QuoteTemplate::findOrFail($templateId);
        $template->duplicate();

        $this->dispatch('notify', type: 'success', message: 'Template duplicated successfully.');
    }

    public function render()
    {
        $query = QuoteTemplate::query()->with('creator');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->byCategory($this->category);
        }

        if (!$this->showIndustryPresets) {
            $query->where('is_industry_preset', false);
        }

        if (!$this->showUserTemplates) {
            $query->where('is_industry_preset', true);
        }

        $templates = $query->latest()->paginate(15);

        $categories = QuoteTemplate::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('livewire.templates.templates-list', [
            'templates' => $templates,
            'categories' => $categories,
        ]);
    }
}
