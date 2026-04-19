@extends('layouts.app')

@section('title', 'Create Maintenance')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Create Maintenance</h1>
            <a href="{{ route('maintenances.index') }}" class="text-gray-600 hover:text-gray-800">Back to Maintenance</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            

            <form method="POST" action="{{ isset($maintenance) ? route('maintenances.update', $maintenance) : route('maintenances.store') }}">
                @csrf
                @if(isset($maintenance)) @method('PUT') @endif
                <?php $model = $maintenance ?? null; ?>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Item Name</label>
                    <input type="text" name="item_name" value="{{ old('item_name', $model->item_name ?? '') }}" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                    <input type="date" name="date" value="{{ old('date', isset($model) ? $model->date->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-md">
                        <option value="Pending" {{ (old('status', $model->status ?? '') == 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ (old('status', $model->status ?? '') == 'In Progress') ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ (old('status', $model->status ?? '') == 'Completed') ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" class="w-full px-3 py-2 border rounded-md">{{ old('description', $model->description ?? '') }}</textarea>
                </div>
                
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create Maintenance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
