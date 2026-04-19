@extends('layouts.app')

@section('title', 'Stock Out Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Stock Out Details</h1>
            <div>
                <a href="{{ route('stock-out.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Back to Stock Out</a>
                <a href="{{ route('stock-out.edit', $stockOut) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Edit</a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Item Name</label>
                <p class="text-gray-900">{{ $stockOut->item->name ?? 'Unknown Item' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                <p class="text-gray-900">{{ $stockOut->quantity }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                <p class="text-gray-900">{{ $stockOut->notes ?: '-' }}</p>
            </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Created By</label>
                <p class="text-gray-900">{{ $stockOut->user->name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                <p class="text-gray-900">{{ $stockOut->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Updated At</label>
                <p class="text-gray-900">{{ $stockOut->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
