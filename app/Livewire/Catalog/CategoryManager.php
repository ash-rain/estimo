<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Models\ActivityLog;
use Livewire\Component;

class CategoryManager extends Component
{
    public $categories;
    public $editingCategoryId = null;
    public $name = '';
    public $description = '';
    public $parent_id = '';
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->loadCategories();
    }

    public function render()
    {
        return view('livewire.catalog.category-manager', [
            'parentCategories' => Category::root()->active()->orderBy('order')->get(),
        ]);
    }

    public function loadCategories()
    {
        $this->categories = Category::with('children')->root()->orderBy('order')->get();
    }

    public function createCategory()
    {
        $this->resetForm();
    }

    public function editCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->parent_id = $category->parent_id ?? '';
        $this->is_active = $category->is_active;
    }

    public function saveCategory()
    {
        $validated = $this->validate();

        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            $category->update($validated);

            ActivityLog::log(
                'category_updated',
                auth()->user()->name . ' updated category: ' . $category->name,
                $category
            );

            session()->flash('success', 'Category updated successfully.');
        } else {
            $category = Category::create($validated);

            ActivityLog::log(
                'category_created',
                auth()->user()->name . ' created category: ' . $category->name,
                $category
            );

            session()->flash('success', 'Category created successfully.');
        }

        $this->resetForm();
        $this->loadCategories();
        $this->dispatch('category-saved');
    }

    public function deleteCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        // Check if category has children or catalog items
        if ($category->children()->count() > 0) {
            session()->flash('error', 'Cannot delete category with subcategories.');
            return;
        }

        if ($category->catalogItems()->count() > 0) {
            session()->flash('error', 'Cannot delete category with catalog items.');
            return;
        }

        ActivityLog::log(
            'category_deleted',
            auth()->user()->name . ' deleted category: ' . $category->name,
            $category
        );

        $category->delete();
        
        $this->loadCategories();
        session()->flash('success', 'Category deleted successfully.');
    }

    public function toggleActive($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'activated' : 'deactivated';

        ActivityLog::log(
            'category_' . $status,
            auth()->user()->name . ' ' . $status . ' category: ' . $category->name,
            $category
        );

        $this->loadCategories();
        session()->flash('success', 'Category ' . $status . ' successfully.');
    }

    public function moveUp($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $previousCategory = Category::where('parent_id', $category->parent_id)
            ->where('order', '<', $category->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousCategory) {
            $tempOrder = $category->order;
            $category->order = $previousCategory->order;
            $previousCategory->order = $tempOrder;

            $category->save();
            $previousCategory->save();

            $this->loadCategories();
        }
    }

    public function moveDown($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $nextCategory = Category::where('parent_id', $category->parent_id)
            ->where('order', '>', $category->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextCategory) {
            $tempOrder = $category->order;
            $category->order = $nextCategory->order;
            $nextCategory->order = $tempOrder;

            $category->save();
            $nextCategory->save();

            $this->loadCategories();
        }
    }

    protected function resetForm()
    {
        $this->editingCategoryId = null;
        $this->name = '';
        $this->description = '';
        $this->parent_id = '';
        $this->is_active = true;
    }
}
