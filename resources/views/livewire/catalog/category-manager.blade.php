<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Category Management</h3>
        <button
            wire:click="createCategory"
            type="button"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Category
        </button>
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

    <!-- Category Form -->
    @if ($editingCategoryId !== null || $name !== '')
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-4">
                {{ $editingCategoryId ? 'Edit Category' : 'New Category' }}
            </h4>
            <form wire:submit.prevent="saveCategory">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input
                            type="text"
                            id="name"
                            wire:model="name"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                        <select
                            id="parent_id"
                            wire:model="parent_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">None (Root Category)</option>
                            @foreach ($parentCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @foreach ($category->children as $child)
                                    <option value="{{ $child->id }}">{{ $category->name }} > {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('parent_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            id="description"
                            wire:model="description"
                            rows="2"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="is_active"
                            wire:model="is_active"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        {{ $editingCategoryId ? 'Update' : 'Create' }}
                    </button>
                    <button
                        type="button"
                        wire:click="$set('editingCategoryId', null); $set('name', '')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Categories List -->
    <div class="space-y-2">
        @forelse ($categories as $category)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <h4 class="text-sm font-medium text-gray-900">{{ $category->name }}</h4>
                            @if (!$category->is_active)
                                <span class="px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded">Inactive</span>
                            @endif
                        </div>
                        @if ($category->description)
                            <p class="mt-1 text-sm text-gray-600">{{ $category->description }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500">{{ $category->items()->count() }} items</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            wire:click="moveUp({{ $category->id }})"
                            class="p-1 text-gray-400 hover:text-gray-600"
                            title="Move up"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        </button>
                        <button
                            wire:click="moveDown({{ $category->id }})"
                            class="p-1 text-gray-400 hover:text-gray-600"
                            title="Move down"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <button
                            wire:click="editCategory({{ $category->id }})"
                            class="p-1 text-blue-600 hover:text-blue-800"
                            title="Edit"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button
                            wire:click="toggleActive({{ $category->id }})"
                            class="p-1 {{ $category->is_active ? 'text-gray-600 hover:text-gray-800' : 'text-green-600 hover:text-green-800' }}"
                            title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $category->is_active ? '18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : '9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                            </svg>
                        </button>
                        <button
                            wire:click="deleteCategory({{ $category->id }})"
                            wire:confirm="Are you sure you want to delete this category?"
                            class="p-1 text-red-600 hover:text-red-800"
                            title="Delete"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Subcategories -->
                @if ($category->children->isNotEmpty())
                    <div class="mt-3 ml-6 space-y-2">
                        @foreach ($category->children as $child)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-700">{{ $child->name }}</span>
                                        @if (!$child->is_active)
                                            <span class="px-2 py-0.5 text-xs font-medium text-gray-600 bg-gray-200 rounded">Inactive</span>
                                        @endif
                                    </div>
                                    @if ($child->description)
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $child->description }}</p>
                                    @endif
                                    <p class="mt-0.5 text-xs text-gray-400">{{ $child->items()->count() }} items</p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        wire:click="editCategory({{ $child->id }})"
                                        class="p-1 text-blue-600 hover:text-blue-800"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="toggleActive({{ $child->id }})"
                                        class="p-1 {{ $child->is_active ? 'text-gray-600 hover:text-gray-800' : 'text-green-600 hover:text-green-800' }}"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $child->is_active ? '18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : '9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="deleteCategory({{ $child->id }})"
                                        wire:confirm="Are you sure you want to delete this category?"
                                        class="p-1 text-red-600 hover:text-red-800"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new category.</p>
            </div>
        @endforelse
    </div>
</div>
