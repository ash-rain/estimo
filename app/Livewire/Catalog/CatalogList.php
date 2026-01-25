<?php

namespace App\Livewire\Catalog;

use App\Models\CatalogItem;
use App\Models\Category;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogList extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = 'active';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $showFormModal = false;
    public $showImportModal = false;
    public $showCategoryModal = false;
    public $editingItemId = null;

    public function render()
    {
        $items = CatalogItem::query()
            ->with(['category', 'creator', 'variants'])
            ->parents() // Only show parent items, not variants
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->categoryFilter, fn($q) => $q->inCategory($this->categoryFilter))
            ->when($this->statusFilter === 'active', fn($q) => $q->active())
            ->when($this->statusFilter === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->statusFilter === 'low_stock', fn($q) => $q->lowStock())
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        $categories = Category::active()->root()->with('children')->orderBy('order')->get();

        return view('livewire.catalog.catalog-list', [
            'items' => $items,
            'categories' => $categories,
            'statuses' => $this->getStatuses(),
            'unitTypes' => $this->getUnitTypes(),
        ]);
    }

    public function createItem()
    {
        $this->editingItemId = null;
        $this->showFormModal = true;
    }

    public function editItem($itemId)
    {
        $this->editingItemId = $itemId;
        $this->showFormModal = true;
    }

    public function deleteItem($itemId)
    {
        $item = CatalogItem::findOrFail($itemId);

        ActivityLog::log(
            'catalog_item_deleted',
            auth()->user()->name . ' deleted catalog item: ' . $item->name,
            $item
        );

        $item->delete();

        session()->flash('success', 'Catalog item deleted successfully.');
    }

    public function duplicateItem($itemId)
    {
        $item = CatalogItem::findOrFail($itemId);

        $duplicate = $item->replicate();
        $duplicate->name = $item->name . ' (Copy)';
        $duplicate->sku = null; // SKU must be unique
        $duplicate->created_by = auth()->id();
        $duplicate->save();

        ActivityLog::log(
            'catalog_item_duplicated',
            auth()->user()->name . ' duplicated catalog item: ' . $item->name,
            $duplicate
        );

        session()->flash('success', 'Catalog item duplicated successfully.');
    }

    public function toggleActive($itemId)
    {
        $item = CatalogItem::findOrFail($itemId);
        $item->is_active = !$item->is_active;
        $item->save();

        $status = $item->is_active ? 'activated' : 'deactivated';

        ActivityLog::log(
            'catalog_item_' . $status,
            auth()->user()->name . ' ' . $status . ' catalog item: ' . $item->name,
            $item
        );

        session()->flash('success', 'Catalog item ' . $status . ' successfully.');
    }

    public function exportItems()
    {
        $items = CatalogItem::query()
            ->with('category')
            ->parents()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->categoryFilter, fn($q) => $q->inCategory($this->categoryFilter))
            ->when($this->statusFilter === 'active', fn($q) => $q->active())
            ->when($this->statusFilter === 'inactive', fn($q) => $q->where('is_active', false))
            ->get();

        $filename = 'catalog_items_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Name',
                'SKU',
                'Description',
                'Category',
                'Cost Price',
                'Selling Price',
                'Currency',
                'Unit Type',
                'Minimum Quantity',
                'Is Taxable',
                'Track Inventory',
                'Stock Quantity',
                'Tags',
                'Notes',
                'Status',
            ]);

            // Data rows
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->name,
                    $item->sku,
                    $item->description,
                    $item->category?->name,
                    $item->cost_price,
                    $item->selling_price,
                    $item->currency,
                    $item->unit_type,
                    $item->minimum_quantity,
                    $item->is_taxable ? 'Yes' : 'No',
                    $item->track_inventory ? 'Yes' : 'No',
                    $item->stock_quantity,
                    is_array($item->tags) ? implode(', ', $item->tags) : '',
                    $item->notes,
                    $item->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'active' => 'Active',
            'inactive' => 'Inactive',
            'low_stock' => 'Low Stock',
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
}
