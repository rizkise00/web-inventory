<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');
        
        $query = \App\Models\User::latest();

        if(!auth()->user()->isManager()) {
            $query->where('id', auth()->id());
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        if ($status) {
            $query->where('is_approved', $status === 'approved' ? 1 : 0);
        }

        $users = $query->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        if(auth()->user()->isManager()) {
            return view('users.create');
        } else {
            return redirect()->route('dashboard');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'manager'])],
            'is_approved' => ['boolean'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_approved' => $request->boolean('is_approved', false),
        ]);

        return redirect()->route('users.index')
            ->with('status', 'User created successfully.');
    }

    public function show(User $user)
    {
        if(auth()->user()->isManager()) {
            return view('users.show', compact('user'));
        } else {
            return redirect()->route('dashboard');
        }
    }

    public function edit(User $user)
    {
        if(auth()->user()->isManager()) {
            return view('users.edit', compact('user'));
        } else {
            return redirect()->route('dashboard');
        }
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'manager'])],
            'is_approved' => ['boolean'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if(auth()->user()->isManager()) {
            $data['role'] = $request->role;
            $data['is_approved'] = $request->has('is_approved');
        }

        if ($request->filled('password')) {
            $request->validate([
                'password' => [Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('status', 'User deleted successfully.');
    }

    public function approve(User $user)
    {
        if(auth()->user()->isManager()) {
            $user->update(['is_approved' => true]);
            return back()->with('success', 'User approved successfully.');
        }
    }

    public function reject(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('status', 'User has been rejected and removed.');
    }
}
