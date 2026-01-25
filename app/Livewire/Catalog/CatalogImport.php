<?php

namespace App\Livewire\Catalog;

use App\Models\CatalogItem;
use App\Models\Category;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CatalogImport extends Component
{
    use WithFileUploads;

    public $file;
    public $importResults = null;
    public $importing = false;
    public $updateExisting = true;

    protected function rules()
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
            'updateExisting' => 'boolean',
        ];
    }

    public function render()
    {
        return view('livewire.catalog.catalog-import');
    }

    public function import()
    {
        $this->validate();

        $this->importing = true;
        $this->importResults = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            $path = $this->file->getRealPath();
            $file = fopen($path, 'r');

            // Read header row
            $header = fgetcsv($file);
            if (!$header) {
                throw new \Exception('Invalid CSV file format.');
            }

            // Validate headers
            $requiredHeaders = ['Name', 'SKU', 'Cost Price', 'Selling Price'];
            $missingHeaders = array_diff($requiredHeaders, $header);
            if (!empty($missingHeaders)) {
                throw new \Exception('Missing required columns: ' . implode(', ', $missingHeaders));
            }

            // Map headers to indices
            $headerMap = array_flip(array_map('trim', $header));

            $lineNumber = 1;
            while (($data = fgetcsv($file)) !== false) {
                $lineNumber++;
                $this->importResults['total']++;

                try {
                    $this->importRow($data, $headerMap);
                } catch (\Exception $e) {
                    $this->importResults['errors'][] = "Line {$lineNumber}: " . $e->getMessage();
                    $this->importResults['skipped']++;
                }
            }

            fclose($file);

            ActivityLog::log(
                'catalog_import',
                auth()->user()->name . ' imported ' . $this->importResults['created'] . ' catalog items',
                null
            );

            session()->flash('success', 'Import completed successfully.');
            $this->dispatch('import-completed');
        } catch (\Exception $e) {
            $this->importResults['errors'][] = $e->getMessage();
            session()->flash('error', 'Import failed: ' . $e->getMessage());
        } finally {
            $this->importing = false;
        }
    }

    protected function importRow($data, $headerMap)
    {
        $name = trim($data[$headerMap['Name']] ?? '');
        if (empty($name)) {
            throw new \Exception('Name is required');
        }

        $sku = trim($data[$headerMap['SKU']] ?? '');
        $description = trim($data[$headerMap['Description']] ?? '');
        $categoryName = trim($data[$headerMap['Category']] ?? '');
        $costPrice = (float) ($data[$headerMap['Cost Price']] ?? 0);
        $sellingPrice = (float) ($data[$headerMap['Selling Price']] ?? 0);
        $currency = strtoupper(trim($data[$headerMap['Currency']] ?? 'USD'));
        $unitType = trim($data[$headerMap['Unit Type']] ?? 'each');
        $minimumQuantity = (float) ($data[$headerMap['Minimum Quantity']] ?? 1);
        $isTaxable = in_array(strtolower(trim($data[$headerMap['Is Taxable']] ?? 'yes')), ['yes', '1', 'true']);
        $trackInventory = in_array(strtolower(trim($data[$headerMap['Track Inventory']] ?? 'no')), ['yes', '1', 'true']);
        $stockQuantity = (int) ($data[$headerMap['Stock Quantity']] ?? 0);
        $tags = trim($data[$headerMap['Tags']] ?? '');
        $notes = trim($data[$headerMap['Notes']] ?? '');
        $isActive = in_array(strtolower(trim($data[$headerMap['Status']] ?? 'active')), ['active', '1', 'true']);

        // Find or create category
        $categoryId = null;
        if (!empty($categoryName)) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'is_active' => true]
            );
            $categoryId = $category->id;
        }

        // Convert tags string to array
        $tagsArray = null;
        if (!empty($tags)) {
            $tagsArray = array_filter(
                array_map('trim', explode(',', $tags)),
                fn($tag) => !empty($tag)
            );
        }

        $itemData = [
            'name' => $name,
            'description' => $description ?: null,
            'category_id' => $categoryId,
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'currency' => $currency,
            'unit_type' => $unitType,
            'minimum_quantity' => $minimumQuantity,
            'is_taxable' => $isTaxable,
            'track_inventory' => $trackInventory,
            'stock_quantity' => $stockQuantity,
            'tags' => $tagsArray,
            'notes' => $notes ?: null,
            'is_active' => $isActive,
            'created_by' => auth()->id(),
        ];

        // Check if item exists by SKU
        if (!empty($sku)) {
            $existingItem = CatalogItem::where('sku', $sku)->first();

            if ($existingItem) {
                if ($this->updateExisting) {
                    $existingItem->update($itemData);
                    $this->importResults['updated']++;
                } else {
                    $this->importResults['skipped']++;
                }
                return;
            }

            $itemData['sku'] = $sku;
        }

        CatalogItem::create($itemData);
        $this->importResults['created']++;
    }

    public function resetImport()
    {
        $this->file = null;
        $this->importResults = null;
        $this->importing = false;
    }

    public function downloadTemplate()
    {
        $filename = 'catalog_import_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
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

            // Example row
            fputcsv($file, [
                'Example Product',
                'PROD-001',
                'This is an example product',
                'General',
                '10.00',
                '20.00',
                'USD',
                'each',
                '1',
                'Yes',
                'No',
                '0',
                'example, sample',
                'Additional notes here',
                'Active',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
