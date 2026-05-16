<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;
    protected $lowStock;

    public function __construct($search = null, $lowStock = null)
    {
        $this->search = $search;
        $this->lowStock = $lowStock;
    }

    public function query()
    {
        return Item::when($this->search, function($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->when($this->lowStock, function($query) {
            return $query->where('stock', '<', 5);
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