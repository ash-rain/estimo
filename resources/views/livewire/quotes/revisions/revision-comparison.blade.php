<div>
    <!-- Compare Button -->
    <button 
        type="button" 
        wire:click="openModal" 
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Compare {{ $currentRevision->version_name }} vs {{ $previousRevision->version_name }}
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full sm:p-6">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Revision Comparison
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Version Headers -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    {{ $previousRevision->version_name }}
                                </span>
                                <span class="text-sm text-gray-600">Previous</span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $previousRevision->created_at->format('M d, Y - H:i') }}</p>
                            @if($previousRevision->notes)
                                <p class="text-sm text-gray-700 mt-2">{{ $previousRevision->notes }}</p>
                            @endif
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    {{ $currentRevision->version_name }}
                                </span>
                                <span class="text-sm text-gray-600">Current</span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $currentRevision->created_at->format('M d, Y - H:i') }}</p>
                            @if($currentRevision->notes)
                                <p class="text-sm text-gray-700 mt-2">{{ $currentRevision->notes }}</p>
                            @endif
                        </div>
                    </div>

                    @if(!$this->hasChanges())
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Changes Detected</h3>
                            <p class="mt-1 text-sm text-gray-500">These revisions are identical.</p>
                        </div>
                    @else
                        <!-- Changes Summary -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Summary of Changes</h4>
                            <p class="text-sm text-blue-700">{{ $currentRevision->getChangeSummary($previousRevision) }}</p>
                        </div>

                        <!-- Detailed Changes -->
                        <div class="space-y-6">
                            <!-- Total Changes -->
                            @if(isset($changes['total']))
                            <div class="border rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Total Amount Changed</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-red-50 border border-red-200 rounded p-3">
                                        <p class="text-xs text-gray-600 mb-1">Previous Total</p>
                                        <p class="text-lg font-semibold text-red-700">${{ number_format($changes['total']['from'], 2) }}</p>
                                    </div>
                                    <div class="bg-green-50 border border-green-200 rounded p-3">
                                        <p class="text-xs text-gray-600 mb-1">New Total</p>
                                        <p class="text-lg font-semibold text-green-700">${{ number_format($changes['total']['to'], 2) }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-center">
                                    <p class="text-sm">
                                        <span class="font-medium">Difference:</span>
                                        <span class="{{ $changes['total']['diff'] > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                            {{ $changes['total']['diff'] > 0 ? '+' : '' }}${{ number_format($changes['total']['diff'], 2) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            @endif

                            <!-- Item Changes -->
                            @if(isset($changes['items']))
                            <div class="border rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Item Changes</h4>

                                <!-- Added Items -->
                                @if(!empty($changes['items']['added']))
                                <div class="mb-4">
                                    <h5 class="text-xs font-semibold text-green-800 uppercase mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Added Items ({{ count($changes['items']['added']) }})
                                    </h5>
                                    <div class="space-y-2">
                                        @foreach($changes['items']['added'] as $item)
                                        <div class="bg-green-50 border border-green-200 rounded p-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $item['name'] }}</p>
                                            <p class="text-xs text-gray-600 mt-1">
                                                {{ $item['quantity'] }} {{ $item['unit'] }} × ${{ number_format($item['unit_price'], 2) }}
                                                = ${{ number_format($item['subtotal'], 2) }}
                                            </p>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Removed Items -->
                                @if(!empty($changes['items']['removed']))
                                <div class="mb-4">
                                    <h5 class="text-xs font-semibold text-red-800 uppercase mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                        Removed Items ({{ count($changes['items']['removed']) }})
                                    </h5>
                                    <div class="space-y-2">
                                        @foreach($changes['items']['removed'] as $item)
                                        <div class="bg-red-50 border border-red-200 rounded p-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $item['name'] }}</p>
                                            <p class="text-xs text-gray-600 mt-1">
                                                {{ $item['quantity'] }} {{ $item['unit'] }} × ${{ number_format($item['unit_price'], 2) }}
                                                = ${{ number_format($item['subtotal'], 2) }}
                                            </p>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Modified Items -->
                                @if(!empty($changes['items']['modified']))
                                <div>
                                    <h5 class="text-xs font-semibold text-blue-800 uppercase mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Modified Items ({{ count($changes['items']['modified']) }})
                                    </h5>
                                    <div class="space-y-2">
                                        @foreach($changes['items']['modified'] as $modified)
                                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                                            <p class="text-sm font-medium text-gray-900 mb-2">{{ $modified['item']['name'] }}</p>
                                            <div class="space-y-1">
                                                @if(isset($modified['changes']['quantity']))
                                                <p class="text-xs">
                                                    <span class="text-gray-600">Quantity:</span>
                                                    <span class="text-red-600 line-through ml-1">{{ $modified['changes']['quantity']['from'] }}</span>
                                                    <span class="mx-1">→</span>
                                                    <span class="text-green-600 font-medium">{{ $modified['changes']['quantity']['to'] }}</span>
                                                </p>
                                                @endif
                                                @if(isset($modified['changes']['unit_price']))
                                                <p class="text-xs">
                                                    <span class="text-gray-600">Unit Price:</span>
                                                    <span class="text-red-600 line-through ml-1">${{ number_format($modified['changes']['unit_price']['from'], 2) }}</span>
                                                    <span class="mx-1">→</span>
                                                    <span class="text-green-600 font-medium">${{ number_format($modified['changes']['unit_price']['to'], 2) }}</span>
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <button 
                        type="button" 
                        wire:click="closeModal"
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
