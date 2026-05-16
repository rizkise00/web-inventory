<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Exports\ItemExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $low_stock = $request->input('low_stock');
        
        $query = \App\Models\Item::with('category')->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($low_stock === '1') {
            $query->where('stock', '<', 5);
        }

        $items = $query->paginate(10)->withQueryString();
        return view('items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Item::create($request->only(['name', 'category_id', 'price', 'description']));

        return redirect()->route('items.index')->with('success', 'Product created successfully.');
    }

    public function show(Item $item)
    {
        $item->load('category');
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $item->update($request->only(['name', 'category_id', 'price', 'description']));

        return redirect()->route('items.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Product deleted successfully.');
    }

    public function export(Request $request)
    {
        if (!auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $search = $request->input('search');
        $low_stock = $request->input('low_stock');

        return Excel::download(new ItemExport($search, $low_stock), 'products.xlsx');
    }
}
