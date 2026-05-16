<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Exports\CategoryExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index()
    {
        $search = request('search');
        $categories = Category::when($search, function($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->paginate(10);
        
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
        if (!auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $search = $request->input('search');

        return Excel::download(new CategoryExport($search), 'categories.xlsx');
    }
}
