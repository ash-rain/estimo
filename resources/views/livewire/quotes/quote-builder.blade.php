<div>
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

    <form wire:submit.prevent="save">
        <!-- Quote Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quote Information</h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client *</label>
                    <select id="client_id" wire:model="client_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        <option value="">Select a client</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} - {{ $client->email }}</option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="title" wire:model="title"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Optional quote title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quote_date" class="block text-sm font-medium text-gray-700 mb-1">Quote Date *</label>
                    <input type="date" id="quote_date" wire:model="quote_date"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                    @error('quote_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label>
                    <input type="date" id="valid_until" wire:model="valid_until"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('valid_until')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" wire:model="description" rows="2"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="mt-4 flex gap-2 flex-wrap">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ $quote->exists ? 'Update Quote' : 'Create Quote' }}
                </button>
                @if ($quote->exists)
                    <button type="button" wire:click="downloadPdf"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </button>
                    <button type="button" wire:click="openSendEmailModal"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Send via Email
                    </button>
                    <button type="button" wire:click="copyPortalLink"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        Copy Client Link
                    </button>
                @endif
            </div>
        </div>

        <!-- Line Items -->
        @if ($quote->exists)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Line Items</h3>
                    <button type="button" wire:click="$set('showAddItemModal', true)"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Qty
                                </th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">Price
                                </th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-24">Disc %
                                </th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">
                                    Subtotal</th>
                                <th class="px-3 py-2 w-20"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($quote->items as $item)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                        @if ($item->description)
                                            <div class="text-xs text-gray-500">{{ $item->description }}</div>
                                        @endif
                                        <div class="text-xs text-gray-400">{{ $item->unit_type }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <input type="number"
                                            wire:change="updateItemQuantity({{ $item->id }}, $event.target.value)"
                                            value="{{ $item->quantity }}" step="0.01" min="0.01"
                                            class="w-20 text-center text-sm rounded border-gray-300">
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <input type="number"
                                            wire:change="updateItemPrice({{ $item->id }}, $event.target.value)"
                                            value="{{ $item->unit_price }}" step="0.01" min="0"
                                            class="w-28 text-right text-sm rounded border-gray-300">
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <input type="number"
                                            wire:change="updateItemDiscount({{ $item->id }}, $event.target.value)"
                                            value="{{ $item->discount_rate }}" step="0.01" min="0"
                                            max="100" class="w-20 text-right text-sm rounded border-gray-300">
                                    </td>
                                    <td class="px-3 py-3 text-right text-sm font-medium text-gray-900">
                                        {{ number_format($item->subtotal, 2) }}
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <button type="button" wire:click="removeItem({{ $item->id }})"
                                            class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-8 text-center text-gray-500">
                                        No items added yet. Click "Add Item" to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Totals Section -->
        @if ($quote->exists && $quote->items->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="max-w-md ml-auto">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-1">Tax Rate
                                (%)</label>
                            <input type="number" id="tax_rate" wire:model.live="tax_rate" step="0.01"
                                min="0" max="100"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="discount_rate" class="block text-sm font-medium text-gray-700 mb-1">Discount
                                (%)</label>
                            <input type="number" id="discount_rate" wire:model.live="discount_rate" step="0.01"
                                min="0" max="100"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">{{ number_format($quote->subtotal, 2) }}</span>
                        </div>
                        @if ($quote->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Discount ({{ $quote->discount_rate }}%):</span>
                                <span
                                    class="font-medium text-red-600">-{{ number_format($quote->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        @if ($quote->tax_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax ({{ $quote->tax_rate }}%):</span>
                                <span class="font-medium">{{ number_format($quote->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                            <span>Total:</span>
                            <span>{{ $currency }} {{ number_format($quote->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Additional Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>

            <div class="space-y-4">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" wire:model="notes" rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Internal notes (not visible to client)"></textarea>
                </div>

                <div>
                    <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">Terms &
                        Conditions</label>
                    <textarea id="terms" wire:model="terms" rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Payment terms, conditions, etc."></textarea>
                </div>

                <div>
                    <label for="footer" class="block text-sm font-medium text-gray-700 mb-1">Footer</label>
                    <textarea id="footer" wire:model="footer" rows="2"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Thank you message, contact info, etc."></textarea>
                </div>
            </div>
        </div>
    </form>

    <!-- Add Item Modal -->
    @if ($showAddItemModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="$set('showAddItemModal', false)"></div>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Item to Quote</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Catalog</label>
                            <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-md">
                                @foreach ($catalogItems as $catalogItem)
                                    <button type="button" wire:click="addItemFromCatalog({{ $catalogItem->id }})"
                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $catalogItem->name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $catalogItem->category?->name ?? 'Uncategorized' }} |
                                                    {{ $catalogItem->unit_type }}</div>
                                            </div>
                                            <div class="text-sm font-medium text-gray-700">
                                                {{ $catalogItem->currency }}
                                                {{ number_format($catalogItem->selling_price, 2) }}
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Or Add Custom Item</label>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" wire:model="newItem.name" placeholder="Item name"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <input type="number" wire:model="newItem.quantity" placeholder="Quantity"
                                    step="0.01"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <input type="number" wire:model="newItem.unit_price" placeholder="Unit price"
                                    step="0.01"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <select wire:model="newItem.unit_type"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="each">Each</option>
                                    <option value="hour">Hour</option>
                                    <option value="sqft">Sq Ft</option>
                                    <option value="lb">Pound</option>
                                </select>
                            </div>
                            <button type="button" wire:click="addCustomItem"
                                class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                Add Custom Item
                            </button>
                        </div>

                        <button type="button" wire:click="$set('showAddItemModal', false)"
                            class="mt-4 w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Send Email Modal -->
    @if ($showSendEmailModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="$set('showSendEmailModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Send Quote via Email
                            </h3>
                            <div class="mt-4 text-left">
                                <div class="mb-4">
                                    <label for="emailRecipient" class="block text-sm font-medium text-gray-700 mb-1">
                                        Recipient Email *
                                    </label>
                                    <input type="email" id="emailRecipient" wire:model="emailRecipient"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="client@example.com" required>
                                    @error('emailRecipient')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="emailMessage" class="block text-sm font-medium text-gray-700 mb-1">
                                        Message (Optional)
                                    </label>
                                    <textarea id="emailMessage" wire:model="emailMessage" rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Add a personal message to include in the email..."></textarea>
                                    @error('emailMessage')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-md p-3 text-sm text-blue-800">
                                    <p><strong>Quote {{ $quote->quote_number }}</strong> will be sent as a PDF
                                        attachment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="button" wire:click="sendEmail"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm">
                            Send Email
                        </button>
                        <button type="button" wire:click="$set('showSendEmailModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @script
        <script>
            // Listen for portal link ready event
            $wire.on('portal-link-ready', (event) => {
                const url = event.url;

                // Copy to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(() => {
                        console.log('Portal link copied to clipboard');
                    }).catch(err => {
                        console.error('Failed to copy:', err);
                        // Fallback method
                        fallbackCopy(url);
                    });
                } else {
                    fallbackCopy(url);
                }
            });

            function fallbackCopy(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                } catch (err) {
                    console.error('Fallback copy failed:', err);
                }
                document.body.removeChild(textArea);
            }
        </script>
    @endscript
</div>
