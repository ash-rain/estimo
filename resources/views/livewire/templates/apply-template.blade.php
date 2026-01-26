<div>
    <!-- Trigger Button -->
    <button 
        type="button"
        @click="$wire.openModal()"
        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50"
    >
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Apply Template
    </button>

    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$wire.closeModal()"></div>

                <!-- Modal panel -->
                <div class="relative inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Apply Template to Quote</h3>
                        <button @click="$wire.closeModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Templates List -->
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            <h4 class="font-medium text-gray-900 mb-3">Select a Template</h4>
                            @foreach($templates as $template)
                                <div 
                                    wire:click="selectTemplate({{ $template->id }})"
                                    class="p-4 border rounded-lg cursor-pointer transition-colors {{ $selectedTemplateId == $template->id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-medium text-gray-900">{{ $template->name }}</h5>
                                        @if($template->is_industry_preset)
                                            <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded">
                                                Preset
                                            </span>
                                        @elseif($template->is_default)
                                            <span class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded">
                                                Default
                                            </span>
                                        @endif
                                    </div>
                                    @if($template->category)
                                        <span class="inline-block px-2 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded mb-2">
                                            {{ ucfirst(str_replace('_', ' ', $template->category)) }}
                                        </span>
                                    @endif
                                    @if($template->description)
                                        <p class="text-sm text-gray-600">{{ Str::limit($template->description, 80) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Template Preview -->
                        <div class="border-l pl-6">
                            <h4 class="font-medium text-gray-900 mb-3">Preview</h4>
                            @if($previewData)
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Template Name</label>
                                        <p class="mt-1 text-gray-900">{{ $previewData['name'] }}</p>
                                    </div>

                                    @if($previewData['description'])
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Description</label>
                                            <p class="mt-1 text-gray-900">{{ $previewData['description'] }}</p>
                                        </div>
                                    @endif

                                    @if($previewData['category'])
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Category</label>
                                            <p class="mt-1 text-gray-900">{{ ucfirst(str_replace('_', ' ', $previewData['category'])) }}</p>
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Items</label>
                                        <p class="mt-1 text-gray-900">{{ $previewData['items_count'] }} item(s)</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Terms & Conditions</label>
                                        <p class="mt-1 text-gray-900">{{ $previewData['has_terms'] ? 'Included' : 'Not included' }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Created By</label>
                                        <p class="mt-1 text-gray-900">{{ $previewData['created_by'] }}</p>
                                    </div>

                                    <div class="pt-4 border-t">
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                            <p class="text-sm text-yellow-800">
                                                <strong>Warning:</strong> Applying this template will update the quote details and add template items. Existing items will not be removed.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-center h-64 text-gray-400">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm">Select a template to preview</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button 
                            wire:click="applyTemplate"
                            :disabled="!$selectedTemplateId"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed"
                        >
                            Apply Template
                        </button>
                        <button 
                            type="button"
                            @click="$wire.closeModal()"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
