<?php

namespace App\Livewire\Quotes;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Quote;
use Livewire\Component;
use Livewire\WithPagination;

class QuoteList extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $clientFilter = '';

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    public function render()
    {
        $quotes = Quote::query()
            ->with(['client', 'creator', 'items'])
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->statusFilter !== 'all', fn ($q) => $q->byStatus($this->statusFilter))
            ->when($this->clientFilter, fn ($q) => $q->forClient($this->clientFilter))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        $clients = Client::active()->orderBy('name')->get();

        return view('livewire.quotes.quote-list', [
            'quotes' => $quotes,
            'clients' => $clients,
            'statuses' => $this->getStatuses(),
        ]);
    }

    public function deleteQuote($quoteId)
    {
        $quote = Quote::findOrFail($quoteId);

        ActivityLog::log(
            'quote_deleted',
            auth()->user()->name.' deleted quote: '.$quote->quote_number,
            $quote
        );

        $quote->delete();

        session()->flash('success', 'Quote deleted successfully.');
    }

    public function duplicateQuote($quoteId)
    {
        $original = Quote::with('items')->findOrFail($quoteId);

        $duplicate = $original->replicate();
        $duplicate->quote_number = Quote::generateQuoteNumber();
        $duplicate->status = 'draft';
        $duplicate->sent_at = null;
        $duplicate->viewed_at = null;
        $duplicate->accepted_at = null;
        $duplicate->rejected_at = null;
        $duplicate->created_by = auth()->id();
        $duplicate->save();

        // Duplicate items
        foreach ($original->items as $item) {
            $duplicateItem = $item->replicate();
            $duplicateItem->quote_id = $duplicate->id;
            $duplicateItem->save();
        }

        ActivityLog::log(
            'quote_duplicated',
            auth()->user()->name.' duplicated quote: '.$original->quote_number,
            $duplicate
        );

        session()->flash('success', 'Quote duplicated successfully.');

        return redirect()->route('quotes.edit', $duplicate->id);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    protected function getStatuses(): array
    {
        return [
            'all' => 'All Quotes',
            'draft' => 'Draft',
            'sent' => 'Sent',
            'viewed' => 'Viewed',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
        ];
    }
}
