<?php

namespace App\Exports;

use App\Models\Maintenance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaintenanceExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;
    protected $status;

    public function __construct($search = null, $status = null)
    {
        $this->search = $search;
        $this->status = $status;
    }

    public function query()
    {
        return Maintenance::when($this->search, function($query, $search) {
            return $query->whereHas('item', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('description', 'like', "%{$search}%");
        })->when($this->status, function($query, $status) {
            return $query->where('status', $status);
        });
    }

    public function headings(): array
    {
        return ['ID', 'Product', 'Quantity', 'Description', 'Date', 'Status', 'User', 'Created At'];
    }

    public function map($maintenance): array
    {
        return [
            $maintenance->id,
            $maintenance->item->name ?? '-',
            $maintenance->quantity,
            $maintenance->description,
            $maintenance->date->format('d M Y'),
            $maintenance->status,
            $maintenance->user->name ?? '-',
            $maintenance->created_at->format('d M Y H:i:s'),
        ];
    }
}