<?php

namespace App\Exports;

use App\Models\Item;

class ItemExport extends ExcelExport
{
    protected $search;
    protected $lowStock;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($search = null, $lowStock = null, $dateFrom = null, $dateTo = null)
    {
        $this->search   = $search;
        $this->lowStock = $lowStock;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function query()
    {
        return Item::with('category')
            ->when($this->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })->when($this->lowStock, function ($query) {
                return $query->where('stock', '<', 5);
            })->when($this->dateFrom, function ($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })->when($this->dateTo, function ($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Category', 'Price', 'Stock', 'Description', 'Created At'];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->category->name ?? '-',
            'Rp ' . number_format($item->price, 0, ',', '.'),
            $item->stock,
            $item->description,
            $item->created_at->format('d M Y H:i:s'),
        ];
    }
}
