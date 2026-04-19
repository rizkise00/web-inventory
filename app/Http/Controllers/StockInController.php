<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Item;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $item_name = $request->input('item_name');
        $user_name = $request->input('user_name');
        
        $query = \App\Models\StockIn::with(['user', 'item'])->latest();

        if ($item_name) {
            $query->whereHas('item', function($q) use ($item_name) {
                $q->where('name', 'like', "%$item_name%");
            });
        }

        if ($user_name) {
            $query->whereHas('user', function($q) use ($user_name) {
                $q->where('name', 'like', "%$user_name%");
            });
        }

        $stockIns = $query->paginate(10)->withQueryString();
        return view('stock-in.index', compact('stockIns'));
    }

    public function create()
    {
        $items = Item::all();
        return view('stock-in.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        StockIn::create([
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('stock-in.index')
            ->with('status', 'Stock in record created successfully.');
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['user', 'item']);
        return view('stock-in.show', compact('stockIn'));
    }

    public function edit(StockIn $stockIn)
    {
        $items = Item::all();
        return view('stock-in.edit', compact('stockIn', 'items'));
    }

    public function update(Request $request, StockIn $stockIn)
    {
        $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $stockIn->update([
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'notes' => $request->notes,
        ]);

        return redirect()->route('stock-in.index')
            ->with('status', 'Stock in record updated successfully.');
    }

    public function destroy(StockIn $stockIn)
    {
        $stockIn->delete();

        return redirect()->route('stock-in.index')
            ->with('status', 'Stock in record deleted successfully.');
    }
}
