<?php

namespace App\Exports;

use App\Models\StockOut;

class StockOutExport extends ExcelExport
{
    protected $itemName;
    protected $userName;
    protected $status;

    public function __construct($itemName = null, $userName = null, $status = null)
    {
        $this->itemName = $itemName;
        $this->userName = $userName;
        $this->status = $status;
    }

    public function query()
    {
        return StockOut::with(['item', 'user'])
            ->when($this->itemName, function ($query, $itemName) {
                return $query->whereHas('item', function ($q) use ($itemName) {
                    $q->where('name', 'like', "%{$itemName}%");
                });
            })
            ->when($this->userName, function ($query, $userName) {
                return $query->whereHas('user', function ($q) use ($userName) {
                    $q->where('name', 'like', "%{$userName}%");
                });
            })
            ->when($this->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest();
    }

    public function headings(): array
    {
        return ['ID', 'Product', 'Quantity', 'Status', 'Unit Price', 'Total Price', 'Notes', 'User', 'Created At'];
    }

    public function map($stockOut): array
    {
        return [
            $stockOut->id,
            $stockOut->item->name ?? '-',
            $stockOut->quantity,
            $stockOut->status,
            'Rp ' . number_format($stockOut->unit_price, 0, ',', '.'),
            'Rp ' . number_format($stockOut->total_price, 0, ',', '.'),
            $stockOut->notes ?? '-',
            $stockOut->user->name ?? '-',
            $stockOut->created_at->format('d M Y H:i:s'),
        ];
    }
}
