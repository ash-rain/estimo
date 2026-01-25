<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Import Catalog Items</h3>
        <p class="mt-1 text-sm text-gray-600">Upload a CSV file to bulk import catalog items. Download the template to
            see the required format.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if (!$importResults)
        <!-- Upload Form -->
        <form wire:submit.prevent="import">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        CSV File *
                    </label>
                    <div class="flex items-center gap-4">
                        <label
                            class="flex-1 flex flex-col items-center px-4 py-6 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="mt-2 text-sm text-gray-600">
                                @if ($file)
                                    {{ $file->getClientOriginalName() }}
                                @else
                                    Click to upload or drag and drop
                                @endif
                            </span>
                            <span class="mt-1 text-xs text-gray-500">CSV or TXT up to 10MB</span>
                            <input type="file" wire:model="file" accept=".csv,.txt" class="hidden" />
                        </label>
                    </div>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="updateExisting" wire:model="updateExisting"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="updateExisting" class="ml-2 text-sm text-gray-700">
                        Update existing items (match by SKU)
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" :disabled="!$wire.file || $wire.importing"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        @if ($importing)
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Importing...
                        @else
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Import
                        @endif
                    </button>

                    <button type="button" wire:click="downloadTemplate"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template
                    </button>
                </div>
            </div>
        </form>

        <!-- Instructions -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <h4 class="text-sm font-medium text-blue-900 mb-2">Import Instructions:</h4>
            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                <li>Download the CSV template to see the required format</li>
                <li>Required columns: Name, SKU, Cost Price, Selling Price</li>
                <li>If a category doesn't exist, it will be created automatically</li>
                <li>SKU must be unique - duplicate SKUs will update existing items (if enabled)</li>
                <li>Boolean fields accept: yes/no, true/false, 1/0</li>
                <li>Maximum file size: 10MB (~10,000+ items)</li>
            </ul>
        </div>
    @else
        <!-- Import Results -->
        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-gray-600">Total Rows</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $importResults['total'] }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-green-600">Created</div>
                    <div class="mt-1 text-2xl font-semibold text-green-900">{{ $importResults['created'] }}</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-blue-600">Updated</div>
                    <div class="mt-1 text-2xl font-semibold text-blue-900">{{ $importResults['updated'] }}</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-yellow-600">Skipped</div>
                    <div class="mt-1 text-2xl font-semibold text-yellow-900">{{ $importResults['skipped'] }}</div>
                </div>
            </div>

            @if (!empty($importResults['errors']))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-red-900 mb-2">Errors ({{ count($importResults['errors']) }}):
                    </h4>
                    <div class="max-h-48 overflow-y-auto">
                        <ul class="text-sm text-red-800 space-y-1">
                            @foreach ($importResults['errors'] as $error)
                                <li class="font-mono text-xs">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="flex gap-3">
                <button type="button" wire:click="resetImport"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Import Another File
                </button>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('import-completed'))"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    @endif
</div>
