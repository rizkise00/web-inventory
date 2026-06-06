<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Item;
use App\Exports\StockInExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $item_name = $request->input('item_name');
        $user_name = $request->input('user_name');
        $date_from = $request->input('date_from');
        $date_to   = $request->input('date_to');

        $query = StockIn::with(['user', 'item'])->latest();

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

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
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

        DB::transaction(function () use ($request) {
            $item = Item::lockForUpdate()->findOrFail($request->item_id);

            StockIn::create([
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'unit_price' => $item->price,
                'total_price' => $item->price * $request->quantity,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);
        });

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

        DB::transaction(function () use ($request, $stockIn) {
            $item = Item::lockForUpdate()->findOrFail($request->item_id);

            $stockIn->update([
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'unit_price' => $item->price,
                'total_price' => $item->price * $request->quantity,
                'notes' => $request->notes,
            ]);
        });

        return redirect()->route('stock-in.index')
            ->with('status', 'Stock in record updated successfully.');
    }

    public function destroy(StockIn $stockIn)
    {
        $stockIn->delete();

        return redirect()->route('stock-in.index')
            ->with('status', 'Stock in record deleted successfully.');
    }

    public function export(Request $request)
    {
        $export = new StockInExport(
            $request->input('item_name'),
            $request->input('user_name'),
            $request->input('date_from'),
            $request->input('date_to')
        );

        if ($request->input('format') === 'pdf') {
            $data = $export->query()->get();
            return Pdf::loadView('exports.pdf.stock-in', compact('data'))
                ->setPaper('a4', 'landscape')
                ->download('stock-in.pdf');
        }

        return $export->download('stock-in.xlsx');
    }
}
