<?php

namespace App\Exports;

use App\Models\StockIn;

class StockInExport extends ExcelExport
{
    protected $itemName;
    protected $userName;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($itemName = null, $userName = null, $dateFrom = null, $dateTo = null)
    {
        $this->itemName = $itemName;
        $this->userName = $userName;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function query()
    {
        return StockIn::with(['item', 'user'])
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
            ->when($this->dateFrom, function ($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($this->dateTo, function ($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest();
    }

    public function headings(): array
    {
        return ['ID', 'Product', 'Quantity', 'Unit Price', 'Total Price', 'Notes', 'User', 'Created At'];
    }

    public function map($stockIn): array
    {
        return [
            $stockIn->id,
            $stockIn->item->name ?? '-',
            $stockIn->quantity,
            'Rp ' . number_format($stockIn->unit_price, 0, ',', '.'),
            'Rp ' . number_format($stockIn->total_price, 0, ',', '.'),
            $stockIn->notes ?? '-',
            $stockIn->user->name ?? '-',
            $stockIn->created_at->format('d M Y H:i:s'),
        ];
    }
}
