<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        if(auth()->user()->isAdmin()) {
            $users = User::paginate(10);
            return view('users.index', compact('users'));
        } else {
            return redirect()->route('dashboard');
        }
    }

    public function create()
    {
        if(auth()->user()->isAdmin()) {
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
            'role' => ['required', Rule::in(['admin', 'user'])],
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
        if(auth()->user()->isAdmin()) {
            return view('users.show', compact('user'));
        } else {
            return redirect()->route('dashboard');
        }
    }

    public function edit(User $user)
    {
        if(auth()->user()->isAdmin()) {
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
            'role' => ['required', Rule::in(['admin', 'user'])],
            'is_approved' => ['boolean'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_approved' => $request->boolean('is_approved', false),
        ];

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
        $user->update(['is_approved' => true]);

        return redirect()->route('users.index')
            ->with('status', "User {$user->name} has been approved.");
    }

    public function reject(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('status', 'User has been rejected and removed.');
    }
}
