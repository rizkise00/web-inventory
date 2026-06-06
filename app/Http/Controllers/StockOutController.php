<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Item;
use App\Exports\StockOutExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $item_name = $request->input('item_name');
        $user_name = $request->input('user_name');
        $status    = $request->input('status');
        $date_from = $request->input('date_from');
        $date_to   = $request->input('date_to');

        $query = StockOut::with(['user', 'item'])->latest();

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

        if ($status) {
            $query->where('status', $status);
        }

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }

        $stockOuts = $query->paginate(10)->withQueryString();
        return view('stock-out.index', compact('stockOuts'));
    }

    public function create()
    {
        $items = Item::all();
        return view('stock-out.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:Consumed,Damaged'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request) {
            $item = Item::lockForUpdate()->findOrFail($request->item_id);

            if ($request->quantity > $item->stock) {
                throw ValidationException::withMessages([
                    'quantity' => ["The quantity exceeds the available stock ({$item->stock})."],
                ]);
            }

            StockOut::create([
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'status' => $request->status,
                'unit_price' => $item->price,
                'total_price' => $item->price * $request->quantity,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('stock-out.index')
            ->with('status', 'Stock out record created successfully.');
    }

    public function show(StockOut $stockOut)
    {
        $stockOut->load(['user', 'item']);
        return view('stock-out.show', compact('stockOut'));
    }

    public function edit(StockOut $stockOut)
    {
        $items = Item::all();
        return view('stock-out.edit', compact('stockOut', 'items'));
    }

    public function update(Request $request, StockOut $stockOut)
    {
        $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:Consumed,Damaged'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $stockOut) {
            $item = Item::lockForUpdate()->findOrFail($request->item_id);

            $available = $item->id == $stockOut->item_id
                ? $item->stock + $stockOut->quantity
                : $item->stock;

            if ($request->quantity > $available) {
                throw ValidationException::withMessages([
                    'quantity' => ["The quantity exceeds the available stock ({$available})."],
                ]);
            }

            $stockOut->update([
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'status' => $request->status,
                'unit_price' => $item->price,
                'total_price' => $item->price * $request->quantity,
                'notes' => $request->notes,
            ]);
        });

        return redirect()->route('stock-out.index')
            ->with('status', 'Stock out record updated successfully.');
    }

    public function destroy(StockOut $stockOut)
    {
        $stockOut->delete();

        return redirect()->route('stock-out.index')
            ->with('status', 'Stock out record deleted successfully.');
    }

    public function export(Request $request)
    {
        $export = new StockOutExport(
            $request->input('item_name'),
            $request->input('user_name'),
            $request->input('status'),
            $request->input('date_from'),
            $request->input('date_to')
        );

        if ($request->input('format') === 'pdf') {
            $data = $export->query()->get();
            return Pdf::loadView('exports.pdf.stock-out', compact('data'))
                ->setPaper('a4', 'landscape')
                ->download('stock-out.pdf');
        }

        return $export->download('stock-out.xlsx');
    }
}
