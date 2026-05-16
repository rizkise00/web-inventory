<?php

namespace App\Exports;

use App\Models\User;

class UserExport extends ExcelExport
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
        return User::when($this->search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })->when($this->role, function ($query, $role) {
            return $query->where('role', $role);
        })->when($this->status, function ($query, $status) {
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
