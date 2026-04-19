@extends('layouts.app')

@section('title', 'Maintenance Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Maintenance Details</h1>
            <div>
                <a href="{{ route('maintenances.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Back to Maintenance</a>
                <a href="{{ route('maintenances.edit', $maintenance) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Edit</a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Item Name</label>
                <p class="text-gray-900">{{ $maintenance->item_name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                <p class="text-gray-900">{{ $maintenance->date->format('Y-m-d') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <div>
                    @if($maintenance->status === 'Completed')
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $maintenance->status }}
                        </span>
                    @elseif($maintenance->status === 'In Progress')
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $maintenance->status }}
                        </span>
                    @else
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            {{ $maintenance->status }}
                        </span>
                    @endif
                </div>
            </div>

            

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <p class="text-gray-900">{{ $maintenance->description ?: '-' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                <p class="text-gray-900">{{ $maintenance->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Updated At</label>
                <p class="text-gray-900">{{ $maintenance->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection