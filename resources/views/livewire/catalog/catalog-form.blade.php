<div class="bg-white p-6">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900">
            {{ $itemId ? 'Edit Catalog Item' : 'New Catalog Item' }}
        </h3>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <!-- Basic Information -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Basic Information</h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" id="name" wire:model="name"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" id="sku" wire:model="sku"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category_id" wire:model="category_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">No Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @foreach ($category->children as $child)
                                <option value="{{ $child->id }}">{{ $category->name }} > {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" wire:model="description" rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Pricing</h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">Cost Price *</label>
                    <input type="number" id="cost_price" wire:model="cost_price" step="0.01" min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                    @error('cost_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">Selling Price
                        *</label>
                    <input type="number" id="selling_price" wire:model="selling_price" step="0.01" min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                    @error('selling_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                    <select id="currency" wire:model="currency"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                        <option value="CAD">CAD</option>
                        <option value="AUD">AUD</option>
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Units & Quantities -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Units & Quantities</h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="unit_type" class="block text-sm font-medium text-gray-700 mb-1">Unit Type *</label>
                    <select id="unit_type" wire:model="unit_type"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        @foreach ($unitTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('unit_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="minimum_quantity" class="block text-sm font-medium text-gray-700 mb-1">Minimum Quantity
                        *</label>
                    <input type="number" id="minimum_quantity" wire:model="minimum_quantity" step="0.01"
                        min="0.01"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                    @error('minimum_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_taxable" wire:model="is_taxable"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_taxable" class="ml-2 text-sm text-gray-700">Taxable</label>
                </div>
            </div>
        </div>

        <!-- Inventory -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Inventory</h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="flex items-center">
                    <input type="checkbox" id="track_inventory" wire:model="track_inventory"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="track_inventory" class="ml-2 text-sm text-gray-700">Track Inventory</label>
                </div>

                @if ($track_inventory)
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Stock
                            Quantity</label>
                        <input type="number" id="stock_quantity" wire:model="stock_quantity" min="0"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('stock_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">Low
                            Stock Alert</label>
                        <input type="number" id="low_stock_threshold" wire:model="low_stock_threshold"
                            min="0"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('low_stock_threshold')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Additional Information</h4>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags
                        (comma-separated)</label>
                    <input type="text" id="tags" wire:model="tags"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., premium, featured, seasonal">
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" wire:model="notes" rows="2"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_active" wire:model="is_active"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" wire:click="$dispatch('cancelled')"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ $itemId ? 'Update Item' : 'Create Item' }}
            </button>
        </div>
    </form>
</div>
