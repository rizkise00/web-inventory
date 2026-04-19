@extends('layouts.app')

@section('title', 'Edit Items')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Items</h1>
            <a href="{{ route('items.index') }}" class="text-gray-600 hover:text-gray-800">Back to Items</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            

            <form method="POST" action="{{ isset($item) ? route('items.update', $item) : route('items.store') }}">
                @csrf
                @if(isset($item)) @method('PUT') @endif
                <?php $model = $item ?? null; ?>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $model->name ?? '') }}" required class="w-full px-3 py-2 border rounded-md">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" class="w-full px-3 py-2 border rounded-md">{{ old('description', $model->description ?? '') }}</textarea>
                </div>
                
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
