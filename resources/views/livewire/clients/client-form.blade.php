<div class="bg-white px-6 py-4">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900">
            {{ $clientId ? 'Edit Client' : 'Add New Client' }}
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            {{ $clientId ? 'Update client information below.' : 'Fill in the details to create a new client.' }}
        </p>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Company Information --}}
        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-4">Company Information</h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="company_name" class="block text-sm font-medium text-gray-700">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="company_name"
                        wire:model="company_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('company_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="contact_name" class="block text-sm font-medium text-gray-700">Contact Name</label>
                    <input
                        type="text"
                        id="contact_name"
                        wire:model="contact_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('contact_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input
                        type="text"
                        id="phone"
                        wire:model="phone"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                    <input
                        type="url"
                        id="website"
                        wire:model="website"
                        placeholder="https://example.com"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('website') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Address Information --}}
        <div class="border-t border-gray-200 pt-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-4">Address Information</h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Street Address</label>
                    <textarea
                        id="address"
                        wire:model="address"
                        rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input
                        type="text"
                        id="city"
                        wire:model="city"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700">State / Province</label>
                    <input
                        type="text"
                        id="state"
                        wire:model="state"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('state') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                    <input
                        type="text"
                        id="postal_code"
                        wire:model="postal_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('postal_code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Country <span class="text-red-500">*</span></label>
                    <select
                        id="country"
                        wire:model="country"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        @foreach($countries as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('country') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Financial Information --}}
        <div class="border-t border-gray-200 pt-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-4">Financial Information</h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Currency <span class="text-red-500">*</span></label>
                    <select
                        id="currency"
                        wire:model="currency"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        @foreach($currencies as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('currency') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                    <input
                        type="number"
                        id="tax_rate"
                        wire:model="tax_rate"
                        step="0.01"
                        min="0"
                        max="100"
                        placeholder="0.00"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('tax_rate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="sm:col-span-2">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="tax_exempt"
                            wire:model="tax_exempt"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="tax_exempt" class="ml-2 block text-sm text-gray-700">
                            Tax Exempt
                        </label>
                    </div>
                    @error('tax_exempt') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Additional Information --}}
        <div class="border-t border-gray-200 pt-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-4">Additional Information</h4>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select
                        id="status"
                        wire:model="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                    <input
                        type="text"
                        id="tags"
                        wire:model="tags"
                        placeholder="Enter tags separated by commas"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <p class="mt-1 text-xs text-gray-500">Separate multiple tags with commas</p>
                    @error('tags') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea
                        id="notes"
                        wire:model="notes"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="border-t border-gray-200 pt-6 flex justify-end gap-3">
            <button
                type="button"
                wire:click="cancel"
                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                {{ $clientId ? 'Update Client' : 'Create Client' }}
            </button>
        </div>
    </form>
</div>
