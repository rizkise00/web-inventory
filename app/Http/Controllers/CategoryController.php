<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Exports\CategoryExport;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $date_from = $request->input('date_from');
        $date_to   = $request->input('date_to');

        $query = Category::latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }

        $categories = $query->paginate(10)->withQueryString();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        Category::create($request->only(['name']));

        return redirect()->route('categories.index')
            ->with('status', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->only(['name']));

        return redirect()->route('categories.index')
            ->with('status', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->items()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Cannot delete category that still has items.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('status', 'Category deleted successfully.');
    }

    public function export(Request $request)
    {
        return (new CategoryExport(
            $request->input('search'),
            $request->input('date_from'),
            $request->input('date_to')
        ))->download('categories.xlsx');
    }
}
