<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quote Templates</h2>
            <p class="text-gray-600 mt-1">Manage and organize your quote templates</p>
        </div>
        <a href="{{ route('templates.create') }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Create Template
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search templates..."
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <select wire:model.live="category"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showIndustryPresets"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Show Industry Presets</span>
                </label>
            </div>

            <div class="flex items-center">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showUserTemplates"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Show My Templates</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                            @if ($template->category)
                                <span
                                    class="inline-block px-2 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded mt-1">
                                    {{ ucfirst(str_replace('_', ' ', $template->category)) }}
                                </span>
                            @endif
                        </div>
                        @if ($template->is_industry_preset)
                            <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded">
                                Preset
                            </span>
                        @elseif($template->is_default)
                            <span class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded">
                                Default
                            </span>
                        @endif
                    </div>

                    @if ($template->description)
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($template->description, 100) }}</p>
                    @endif

                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                        @if ($template->hasItems())
                            <span>{{ count($template->template_data['items'] ?? []) }} items</span>
                        @endif
                        <span>Created by {{ $template->creator?->name ?? 'System' }}</span>
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="duplicateTemplate({{ $template->id }})"
                            class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Duplicate
                        </button>
                        @if (!$template->is_industry_preset)
                            <button wire:click="deleteTemplate({{ $template->id }})"
                                wire:confirm="Are you sure you want to delete this template?"
                                class="px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No templates found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new template.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($templates->hasPages())
        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @endif
</div>
