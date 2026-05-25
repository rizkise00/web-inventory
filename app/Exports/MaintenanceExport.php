<?php

namespace App\Exports;

use App\Models\Maintenance;

class MaintenanceExport extends ExcelExport
{
    protected $search;
    protected $status;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($search = null, $status = null, $dateFrom = null, $dateTo = null)
    {
        $this->search   = $search;
        $this->status   = $status;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function query()
    {
        return Maintenance::with(['item', 'user'])
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('item', function ($qi) use ($search) {
                        $qi->where('name', 'like', "%{$search}%");
                    })->orWhere('description', 'like', "%{$search}%");
                });
            })->when($this->status, function ($query, $status) {
                return $query->where('status', $status);
            })->when($this->dateFrom, function ($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })->when($this->dateTo, function ($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
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
