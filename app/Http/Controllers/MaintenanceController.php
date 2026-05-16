<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Item;
use App\Exports\MaintenanceExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $query = Maintenance::with(['item', 'user'])->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('item', function($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%");
                })->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $maintenances = $query->paginate(10)->withQueryString();
        return view('maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $items = Item::all();
        return view('maintenances.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|string|in:Pending,In Progress,Completed',
        ]);

        DB::transaction(function () use ($request) {
            $item = Item::lockForUpdate()->findOrFail($request->item_id);

            if ($item->stock < $request->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => ["Insufficient stock. Available: {$item->stock}"],
                ]);
            }

            $maintenance = Maintenance::create([
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'description' => $request->description,
                'date' => $request->date,
                'status' => $request->status,
                'user_id' => auth()->id(),
            ]);

            if ($maintenance->status !== 'Completed') {
                $item->decrement('stock', $request->quantity);
            }
        });

        return redirect()->route('maintenances.index')->with('status', 'Maintenance record created successfully.');
    }

    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['item', 'user']);
        return view('maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        $items = Item::all();
        return view('maintenances.edit', compact('maintenance', 'items'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|string|in:Pending,In Progress,Completed',
        ]);

        try {
            DB::transaction(function() use ($request, $maintenance) {
                $oldItemId = $maintenance->item_id;
                $newItemId = $request->item_id;
                $oldStatus = $maintenance->status;
                $newStatus = $request->status;
                $oldQty = $maintenance->quantity;
                $newQty = $request->quantity;

                if ($oldItemId != $newItemId) {
                    // 1. Item Changed
                    // Restore stock to old item if it was in maintenance
                    if ($oldStatus !== 'Completed') {
                        $oldItem = Item::lockForUpdate()->find($oldItemId);
                        if ($oldItem) {
                            $oldItem->increment('stock', $oldQty);
                        }
                    }

                    // Deduct stock from new item if it is going into maintenance
                    if ($newStatus !== 'Completed') {
                        $newItem = Item::lockForUpdate()->findOrFail($newItemId);
                        if ($newItem->stock < $newQty) {
                            throw new \Exception("Insufficient stock on the new product. Available: {$newItem->stock}");
                        }
                        $newItem->decrement('stock', $newQty);
                    }
                } else {
                    // 2. Same Item
                    $item = Item::lockForUpdate()->findOrFail($newItemId);

                    if ($oldStatus !== 'Completed' && $newStatus === 'Completed') {
                        // Status changed to Completed: return stock
                        $item->increment('stock', $oldQty);
                    } 
                    elseif ($oldStatus === 'Completed' && $newStatus !== 'Completed') {
                        // Status changed from Completed: deduct stock
                        if ($item->stock < $newQty) {
                            throw new \Exception("Insufficient stock to move back to maintenance. Available: {$item->stock}");
                        }
                        $item->decrement('stock', $newQty);
                    }
                    elseif ($oldStatus !== 'Completed' && $newStatus !== 'Completed') {
                        // Status remained NOT Completed: adjust based on quantity change
                        if ($oldQty !== $newQty) {
                            $diff = $newQty - $oldQty;
                            if ($diff > 0) {
                                if ($item->stock < $diff) {
                                    throw new \Exception("Insufficient stock to increase maintenance quantity. Available: {$item->stock}");
                                }
                                $item->decrement('stock', $diff);
                            } else {
                                $item->increment('stock', abs($diff));
                            }
                        }
                    }
                    // If both are Completed, no stock change needed even if Qty changed
                }

                $maintenance->update($request->only([
                    'item_id', 'quantity', 'description', 'date', 'status',
                ]));
            });

            return redirect()->route('maintenances.index')->with('status', 'Maintenance record updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Maintenance $maintenance)
    {
        DB::transaction(function() use ($maintenance) {
            if ($maintenance->status !== 'Completed' && $maintenance->item) {
                $maintenance->item->increment('stock', $maintenance->quantity);
            }
            $maintenance->delete();
        });

        return redirect()->route('maintenances.index')->with('status', 'Maintenance record deleted successfully.');
    }

    public function export(Request $request)
    {
        if (!auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $search = $request->input('search');
        $status = $request->input('status');

        return Excel::download(new MaintenanceExport($search, $status), 'maintenances.xlsx');
    }
}
