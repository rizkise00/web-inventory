@extends('layouts.app')

@section('title', 'Stock Masuk Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Stock Masuk Details</h1>
            <div>
                <a href="{{ route('stock-in.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Back to Stock Masuk</a>
                <a href="{{ route('stock-in.edit', $stockIn) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Edit</a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Item Name</label>
                <p class="text-gray-900">{{ $stockIn->item_name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                <p class="text-gray-900">{{ $stockIn->quantity }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Supplier</label>
                <p class="text-gray-900">{{ $stockIn->supplier ?: '-' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                <p class="text-gray-900">{{ $stockIn->date->format('Y-m-d') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                <p class="text-gray-900">{{ $stockIn->notes ?: '-' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Created By</label>
                <p class="text-gray-900">{{ $stockIn->user->name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                <p class="text-gray-900">{{ $stockIn->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Updated At</label>
                <p class="text-gray-900">{{ $stockIn->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
