<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function index()
    {
        $stockOuts = StockOut::with('user')->latest()->paginate(10);
        return view('stock-out.index', compact('stockOuts'));
    }

    public function create()
    {
        return view('stock-out.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        StockOut::create([
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'recipient' => $request->recipient,
            'date' => $request->date,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('stock-out.index')
            ->with('status', 'Stock out record created successfully.');
    }

    public function show(StockOut $stockOut)
    {
        return view('stock-out.show', compact('stockOut'));
    }

    public function edit(StockOut $stockOut)
    {
        return view('stock-out.edit', compact('stockOut'));
    }

    public function update(Request $request, StockOut $stockOut)
    {
        $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $stockOut->update([
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'recipient' => $request->recipient,
            'date' => $request->date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('stock-out.index')
            ->with('status', 'Stock out record updated successfully.');
    }

    public function destroy(StockOut $stockOut)
    {
        $stockOut->delete();

        return redirect()->route('stock-out.index')
            ->with('status', 'Stock out record deleted successfully.');
    }
}
