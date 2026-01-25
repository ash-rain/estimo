<?php

namespace App\Livewire\Quotes;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\QuoteEmail;
use App\Models\Client;
use App\Models\CatalogItem;
use App\Models\ActivityLog;
use App\Services\PdfGenerator;
use App\Mail\QuoteSent;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class QuoteBuilder extends Component
{
    public ?int $quoteId = null;
    public Quote $quote;

    // Quote fields
    public $client_id = '';
    public $title = '';
    public $description = '';
    public $quote_date = '';
    public $valid_until = '';
    public $tax_rate = 0;
    public $discount_rate = 0;
    public $notes = '';
    public $terms = '';
    public $footer = '';
    public $currency = 'USD';

    // UI state
    public $showAddItemModal = false;
    public $showSendEmailModal = false;
    public $editingItemIndex = null;

    // Email form
    public $emailRecipient = '';
    public $emailMessage = '';

    // New item form
    public $newItem = [
        'catalog_item_id' => '',
        'name' => '',
        'description' => '',
        'quantity' => 1,
        'unit_price' => 0,
        'unit_type' => 'each',
        'is_taxable' => true,
        'discount_rate' => 0,
    ];

    protected function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quote_date' => 'required|date',
            'valid_until' => 'nullable|date|after:quote_date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'discount_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'footer' => 'nullable|string',
            'currency' => 'required|string|size:3',
        ];
    }

    public function mount()
    {
        if ($this->quoteId) {
            $this->quote = Quote::with('items')->findOrFail($this->quoteId);
            $this->client_id = $this->quote->client_id;
            $this->title = $this->quote->title ?? '';
            $this->description = $this->quote->description ?? '';
            $this->quote_date = $this->quote->quote_date->format('Y-m-d');
            $this->valid_until = $this->quote->valid_until ? $this->quote->valid_until->format('Y-m-d') : '';
            $this->tax_rate = $this->quote->tax_rate;
            $this->discount_rate = $this->quote->discount_rate;
            $this->notes = $this->quote->notes ?? '';
            $this->terms = $this->quote->terms ?? '';
            $this->footer = $this->quote->footer ?? '';
            $this->currency = $this->quote->currency;
            
            // Pre-fill email recipient with client email
            if ($this->quote->client && $this->quote->client->email) {
                $this->emailRecipient = $this->quote->client->email;
            }
        } else {
            $this->quote = new Quote();
            $this->quote_date = now()->format('Y-m-d');
            $this->valid_until = now()->addDays(30)->format('Y-m-d');
        }
    }

    public function render()
    {
        $clients = Client::active()->orderBy('name')->get();
        $catalogItems = CatalogItem::with('category')->active()->parents()->orderBy('name')->get();

        return view('livewire.quotes.quote-builder', [
            'clients' => $clients,
            'catalogItems' => $catalogItems,
        ]);
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->quote->exists) {
            $this->quote->update($validated);

            ActivityLog::log(
                'quote_updated',
                auth()->user()->name . ' updated quote: ' . $this->quote->quote_number,
                $this->quote
            );

            session()->flash('success', 'Quote updated successfully.');
        } else {
            $validated['created_by'] = auth()->id();
            $validated['status'] = 'draft';
            $this->quote = Quote::create($validated);

            ActivityLog::log(
                'quote_created',
                auth()->user()->name . ' created quote: ' . $this->quote->quote_number,
                $this->quote
            );

            session()->flash('success', 'Quote created successfully.');
            return redirect()->route('quotes.edit', $this->quote->id);
        }
    }

    public function addItemFromCatalog($catalogItemId)
    {
        $catalogItem = CatalogItem::findOrFail($catalogItemId);

        if (!$this->quote->exists) {
            session()->flash('error', 'Please save the quote first before adding items.');
            return;
        }

        $itemData = QuoteItem::createFromCatalogItem($catalogItem);
        $itemData['quote_id'] = $this->quote->id;
        $itemData['sort_order'] = $this->quote->items()->count();

        QuoteItem::create($itemData);

        $this->quote->refresh();
        $this->showAddItemModal = false;

        session()->flash('success', 'Item added to quote.');
    }

    public function addCustomItem()
    {
        if (!$this->quote->exists) {
            session()->flash('error', 'Please save the quote first before adding items.');
            return;
        }

        $this->validate([
            'newItem.name' => 'required|string|max:255',
            'newItem.quantity' => 'required|numeric|min:0.01',
            'newItem.unit_price' => 'required|numeric|min:0',
        ]);

        $itemData = $this->newItem;
        $itemData['quote_id'] = $this->quote->id;
        $itemData['sort_order'] = $this->quote->items()->count();

        QuoteItem::create($itemData);

        $this->quote->refresh();
        $this->resetNewItem();
        $this->showAddItemModal = false;

        session()->flash('success', 'Custom item added to quote.');
    }

    public function removeItem($itemId)
    {
        $item = QuoteItem::findOrFail($itemId);
        $item->delete();

        $this->quote->refresh();
    }

    public function updateItemQuantity($itemId, $quantity)
    {
        $item = QuoteItem::findOrFail($itemId);
        $item->quantity = max(0.01, (float) $quantity);
        $item->save();

        $this->quote->refresh();
    }

    public function updateItemPrice($itemId, $price)
    {
        $item = QuoteItem::findOrFail($itemId);
        $item->unit_price = max(0, (float) $price);
        $item->save();

        $this->quote->refresh();
    }

    public function updateItemDiscount($itemId, $discount)
    {
        $item = QuoteItem::findOrFail($itemId);
        $item->discount_rate = max(0, min(100, (float) $discount));
        $item->save();

        $this->quote->refresh();
    }

    public function reorderItems($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            QuoteItem::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->quote->refresh();
    }

    public function sendQuote()
    {
        if (!$this->quote->exists || $this->quote->items()->count() === 0) {
            session()->flash('error', 'Cannot send an empty quote.');
            return;
        }

        $this->quote->markAsSent();

        ActivityLog::log(
            'quote_sent',
            auth()->user()->name . ' sent quote: ' . $this->quote->quote_number,
            $this->quote
        );

        session()->flash('success', 'Quote sent successfully.');
    }

    public function openSendEmailModal()
    {
        if (!$this->quote->exists || $this->quote->items()->count() === 0) {
            session()->flash('error', 'Cannot send an empty quote.');
            return;
        }

        // Pre-fill email if not already set
        if (empty($this->emailRecipient) && $this->quote->client && $this->quote->client->email) {
            $this->emailRecipient = $this->quote->client->email;
        }

        $this->showSendEmailModal = true;
    }

    public function sendEmail()
    {
        $this->validate([
            'emailRecipient' => 'required|email',
            'emailMessage' => 'nullable|string',
        ]);

        try {
            // Send email with PDF attachment
            Mail::to($this->emailRecipient)
                ->send(new QuoteSent($this->quote, $this->emailMessage));

            // Log the email
            QuoteEmail::create([
                'quote_id' => $this->quote->id,
                'recipient_email' => $this->emailRecipient,
                'recipient_name' => $this->quote->client->name,
                'message' => $this->emailMessage,
                'status' => 'sent',
            ]);

            // Mark quote as sent
            $this->quote->markAsSent();

            // Log activity
            ActivityLog::log(
                'quote_emailed',
                auth()->user()->name . ' emailed quote ' . $this->quote->quote_number . ' to ' . $this->emailRecipient,
                $this->quote
            );

            $this->showSendEmailModal = false;
            $this->emailMessage = '';

            session()->flash('success', 'Quote sent via email successfully!');
        } catch (\Exception $e) {
            // Log failed email
            QuoteEmail::create([
                'quote_id' => $this->quote->id,
                'recipient_email' => $this->emailRecipient,
                'recipient_name' => $this->quote->client->name,
                'message' => $this->emailMessage,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            session()->flash('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function downloadPdf()
    {
        if (!$this->quote->exists || $this->quote->items()->count() === 0) {
            session()->flash('error', 'Cannot download PDF for an empty quote.');
            return;
        }

        $pdfGenerator = app(PdfGenerator::class);
        
        ActivityLog::log(
            'quote_downloaded',
            auth()->user()->name . ' downloaded PDF for quote: ' . $this->quote->quote_number,
            $this->quote
        );

        return $pdfGenerator->downloadQuotePdf($this->quote);
    }

    protected function resetNewItem()
    {
        $this->newItem = [
            'catalog_item_id' => '',
            'name' => '',
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'unit_type' => 'each',
            'is_taxable' => true,
            'discount_rate' => 0,
        ];
    }
}
