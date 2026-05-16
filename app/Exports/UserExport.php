<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;
    protected $role;
    protected $status;

    public function __construct($search = null, $role = null, $status = null)
    {
        $this->search = $search;
        $this->role = $role;
        $this->status = $status;
    }

    public function query()
    {
        return User::when($this->search, function($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })->when($this->role, function($query, $role) {
            return $query->where('role', $role);
        })->when($this->status, function($query, $status) {
            return $query->where('is_approved', $status === 'approved' ? 1 : 0);
        });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Role', 'Approved', 'Created At'];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role,
            $user->is_approved ? 'Yes' : 'No',
            $user->created_at->format('d M Y H:i:s'),
        ];
    }
}