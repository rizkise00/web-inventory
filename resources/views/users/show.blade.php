@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">User Details</h1>
            <div>
                <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Back to Users</a>
                <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Edit</a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                <p class="text-gray-900">{{ $user->name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <p class="text-gray-900">{{ $user->email }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $user->is_approved ? 'Approved' : 'Pending' }}
                </span>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                <p class="text-gray-900">{{ $user->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Updated At</label>
                <p class="text-gray-900">{{ $user->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
