<div class="bg-white px-6 py-4">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900">Import Clients from CSV</h3>
        <p class="mt-1 text-sm text-gray-500">
            Upload a CSV file to bulk import clients. Download the template to see the required format.
        </p>
    </div>

    <form wire:submit.prevent="importClients" class="space-y-6">
        {{-- Download Template Button --}}
        <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-md">
            <div>
                <p class="text-sm font-medium text-blue-900">Need a template?</p>
                <p class="text-xs text-blue-700 mt-1">Download our CSV template with the correct format and sample data.</p>
            </div>
            <button
                type="button"
                wire:click="downloadTemplate"
                class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Template
            </button>
        </div>

        {{-- File Upload --}}
        <div>
            <label for="csvFile" class="block text-sm font-medium text-gray-700 mb-2">
                Select CSV File <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <label for="csvFile" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Upload a file</span>
                            <input
                                id="csvFile"
                                type="file"
                                wire:model="csvFile"
                                accept=".csv"
                                class="sr-only"
                            >
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">CSV files up to 10MB</p>
                </div>
            </div>

            @if ($csvFile)
                <div class="mt-2 text-sm text-gray-600">
                    <span class="font-medium">Selected file:</span> {{ $csvFile->getClientOriginalName() }}
                    <span class="text-gray-500">({{ number_format($csvFile->getSize() / 1024, 2) }} KB)</span>
                </div>
            @endif

            @error('csvFile')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Import Results --}}
        @if($importResults)
            <div class="space-y-3">
                @if($importResults['success'] > 0)
                    <div class="p-4 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    Successfully imported {{ $importResults['success'] }} {{ Str::plural('client', $importResults['success']) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($importResults['failed'] > 0)
                    <div class="p-4 bg-red-50 border border-red-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-red-800">
                                    Failed to import {{ $importResults['failed'] }} {{ Str::plural('client', $importResults['failed']) }}
                                </h4>
                                @if(count($importResults['errors']) > 0)
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($importResults['errors'] as $error)
                                                <li>
                                                    <strong>Row {{ $error['row'] }}:</strong> {{ $error['company'] }}
                                                    <ul class="ml-5 mt-1 space-y-1">
                                                        @foreach($error['errors'] as $msg)
                                                            <li class="text-xs">{{ $msg }}</li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Form Actions --}}
        <div class="border-t border-gray-200 pt-6 flex justify-end gap-3">
            <button
                type="button"
                wire:click="cancel"
                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                {{ $importResults && $importResults['success'] > 0 ? 'Close' : 'Cancel' }}
            </button>
            @if(!$importResults || $importResults['failed'] > 0)
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="importClients">Import Clients</span>
                    <span wire:loading wire:target="importClients">Processing...</span>
                </button>
            @endif
        </div>
    </form>
</div>
