<?php

namespace App\Exports;

use App\Models\Category;

class CategoryExport extends ExcelExport
{
    protected $search;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($search = null, $dateFrom = null, $dateTo = null)
    {
        $this->search   = $search;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function query()
    {
        return Category::when($this->search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->when($this->dateFrom, function ($query, $dateFrom) {
            return $query->whereDate('created_at', '>=', $dateFrom);
        })->when($this->dateTo, function ($query, $dateTo) {
            return $query->whereDate('created_at', '<=', $dateTo);
        });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Created At'];
    }

    public function map($category): array
    {
        return [
            $category->id,
            $category->name,
            $category->created_at->format('d M Y H:i:s'),
        ];
    }
}
