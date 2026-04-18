<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::with('user')->latest()->paginate(10);
        return view('stock-in.index', compact('stockIns'));
    }

    public function create()
    {
        return view('stock-in.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        StockIn::create([
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'supplier' => $request->supplier,
            'date' => $request->date,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('stock-in.index')
            ->with('status', 'Stock in record created successfully.');
    }

    public function show(StockIn $stockIn)
    {
        return view('stock-in.show', compact('stockIn'));
    }

    public function edit(StockIn $stockIn)
    {
        return view('stock-in.edit', compact('stockIn'));
    }

    public function update(Request $request, StockIn $stockIn)
    {
        $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $stockIn->update([
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'supplier' => $request->supplier,
            'date' => $request->date,
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
