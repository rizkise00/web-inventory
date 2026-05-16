<?php

namespace App\Exports;

use App\Models\Category;

class CategoryExport extends ExcelExport
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return Category::when($this->search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
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
