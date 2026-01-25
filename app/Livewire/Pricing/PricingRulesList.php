<?php

namespace App\Livewire\Pricing;

use App\Models\PricingRule;
use Livewire\Component;
use Livewire\WithPagination;

class PricingRulesList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterActive = 'active';

    protected $queryString = ['search', 'filterType', 'filterActive'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleActive($ruleId)
    {
        $rule = PricingRule::findOrFail($ruleId);
        $rule->update(['active' => !$rule->active]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Pricing rule ' . ($rule->active ? 'activated' : 'deactivated'),
        ]);
    }

    public function deleteRule($ruleId)
    {
        PricingRule::findOrFail($ruleId)->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Pricing rule deleted successfully',
        ]);
    }

    public function render()
    {
        $query = PricingRule::query();

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterActive === 'active') {
            $query->where('active', true);
        } elseif ($this->filterActive === 'inactive') {
            $query->where('active', false);
        }

        $rules = $query->byPriority()->paginate(20);

        return view('livewire.pricing.pricing-rules-list', [
            'rules' => $rules,
        ]);
    }
}
