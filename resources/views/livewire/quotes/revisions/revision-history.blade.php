<div>
    <!-- View History Button -->
    <button 
        type="button" 
        wire:click="openModal" 
        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        View History
        @if($quote->revisions->count() > 0)
            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-800">
                {{ $quote->revisions->count() }}
            </span>
        @endif
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Revision History
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @if($revisions->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No revisions yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Create your first revision to start tracking changes.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($revisions as $revision)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors @if($selectedRevisionId === $revision->id) ring-2 ring-indigo-500 @endif">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800">
                                                {{ $revision->version_name }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                {{ $revision->created_at->format('M d, Y - H:i') }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                by {{ $revision->creator->name }}
                                            </span>
                                        </div>

                                        @if($revision->notes)
                                        <p class="mt-2 text-sm text-gray-700">{{ $revision->notes }}</p>
                                        @endif

                                        <!-- Show changes if we have a previous revision -->
                                        @if($revision->parentRevision)
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-500">
                                                <span class="font-medium">Changes:</span>
                                                {{ $revision->getChangeSummary($revision->parentRevision) }}
                                            </p>
                                        </div>
                                        @endif

                                        <!-- Details when selected -->
                                        @if($selectedRevisionId === $revision->id)
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <h4 class="text-xs font-semibold text-gray-700 uppercase mb-2">Quote Details</h4>
                                                    <dl class="space-y-1">
                                                        <div class="flex justify-between text-sm">
                                                            <dt class="text-gray-600">Subtotal:</dt>
                                                            <dd class="font-medium">${{ number_format($revision->totals['subtotal'], 2) }}</dd>
                                                        </div>
                                                        <div class="flex justify-between text-sm">
                                                            <dt class="text-gray-600">Tax:</dt>
                                                            <dd class="font-medium">${{ number_format($revision->totals['tax'], 2) }}</dd>
                                                        </div>
                                                        <div class="flex justify-between text-sm">
                                                            <dt class="text-gray-600">Discount:</dt>
                                                            <dd class="font-medium">${{ number_format($revision->totals['discount'], 2) }}</dd>
                                                        </div>
                                                        <div class="flex justify-between text-sm font-semibold border-t pt-1">
                                                            <dt class="text-gray-900">Total:</dt>
                                                            <dd class="text-indigo-600">${{ number_format($revision->totals['total'], 2) }}</dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-semibold text-gray-700 uppercase mb-2">Items ({{ count($revision->items) }})</h4>
                                                    <ul class="space-y-1 max-h-32 overflow-y-auto">
                                                        @foreach($revision->items as $item)
                                                        <li class="text-sm text-gray-600">
                                                            {{ $item['name'] }} ({{ $item['quantity'] }} × ${{ number_format($item['unit_price'], 2) }})
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="ml-4 flex flex-col space-y-2">
                                        <button 
                                            wire:click="selectRevision({{ $revision->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50"
                                        >
                                            @if($selectedRevisionId === $revision->id)
                                                Hide Details
                                            @else
                                                View Details
                                            @endif
                                        </button>

                                        @if($revision->revision_number < $quote->getCurrentRevisionNumber() || $quote->getCurrentRevisionNumber() === 0)
                                        <button 
                                            wire:click="restoreRevision({{ $revision->id }})"
                                            wire:confirm="Are you sure you want to restore this revision? This will update the current quote data."
                                            class="inline-flex items-center px-3 py-1.5 border border-indigo-300 rounded-md text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Restore
                                        </button>
                                        @endif

                                        @if($revision->parentRevision && !$compareRevisionId)
                                        <button 
                                            wire:click="setCompareRevision({{ $revision->parentRevision->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Compare
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-5 sm:mt-6">
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
