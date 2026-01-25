<?php

namespace App\Livewire\Catalog;

use App\Models\ActivityLog;
use App\Models\CatalogItem;
use App\Models\Category;
use Livewire\Component;

class CatalogForm extends Component
{
    public ?int $itemId = null;

    // Basic Information
    public $name = '';

    public $sku = '';

    public $description = '';

    public $category_id = '';

    // Pricing
    public $cost_price = 0;

    public $selling_price = 0;

    public $currency = 'USD';

    // Units & Quantities
    public $unit_type = 'each';

    public $minimum_quantity = 1;

    public $is_taxable = true;

    // Inventory
    public $track_inventory = false;

    public $stock_quantity = 0;

    public $low_stock_threshold = null;

    // Additional
    public $tags = '';

    public $notes = '';

    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:catalog_items,sku,'.($this->itemId ?? 'NULL'),
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'unit_type' => 'required|string',
            'minimum_quantity' => 'required|numeric|min:0.01',
            'is_taxable' => 'boolean',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        if ($this->itemId) {
            $item = CatalogItem::findOrFail($this->itemId);

            $this->name = $item->name;
            $this->sku = $item->sku ?? '';
            $this->description = $item->description ?? '';
            $this->category_id = $item->category_id ?? '';
            $this->cost_price = $item->cost_price;
            $this->selling_price = $item->selling_price;
            $this->currency = $item->currency;
            $this->unit_type = $item->unit_type;
            $this->minimum_quantity = $item->minimum_quantity;
            $this->is_taxable = $item->is_taxable;
            $this->track_inventory = $item->track_inventory;
            $this->stock_quantity = $item->stock_quantity;
            $this->low_stock_threshold = $item->low_stock_threshold;
            $this->notes = $item->notes ?? '';
            $this->tags = is_array($item->tags) ? implode(', ', $item->tags) : '';
            $this->is_active = $item->is_active;
        }
    }

    public function save()
    {
        $validated = $this->validate();

        // Convert tags string to array
        $tagsArray = array_filter(
            array_map('trim', explode(',', $validated['tags'] ?? '')),
            fn ($tag) => ! empty($tag)
        );
        $validated['tags'] = ! empty($tagsArray) ? $tagsArray : null;

        if ($this->itemId) {
            $item = CatalogItem::findOrFail($this->itemId);
            $item->update($validated);

            ActivityLog::log(
                'catalog_item_updated',
                auth()->user()->name.' updated catalog item: '.$item->name,
                $item
            );

            session()->flash('success', 'Catalog item updated successfully.');
        } else {
            $validated['created_by'] = auth()->id();
            $item = CatalogItem::create($validated);

            ActivityLog::log(
                'catalog_item_created',
                auth()->user()->name.' created catalog item: '.$item->name,
                $item
            );

            session()->flash('success', 'Catalog item created successfully.');
        }

        $this->dispatch('saved');
    }

    public function cancel()
    {
        $this->dispatch('cancelled');
    }

    public function render()
    {
        return view('livewire.catalog.catalog-form', [
            'categories' => Category::with('children')->root()->active()->orderBy('order')->get(),
            'currencies' => $this->getCurrencies(),
            'unitTypes' => $this->getUnitTypes(),
        ]);
    }

    protected function getCurrencies(): array
    {
        return [
            'USD' => 'USD - US Dollar',
            'EUR' => 'EUR - Euro',
            'GBP' => 'GBP - British Pound',
            'CAD' => 'CAD - Canadian Dollar',
            'AUD' => 'AUD - Australian Dollar',
        ];
    }

    protected function getUnitTypes(): array
    {
        return [
            'each' => 'Each',
            'hour' => 'Hour',
            'sqft' => 'Square Foot',
            'lft' => 'Linear Foot',
            'lb' => 'Pound',
            'kg' => 'Kilogram',
            'gal' => 'Gallon',
            'liter' => 'Liter',
            'box' => 'Box',
            'case' => 'Case',
        ];
    }
}
