@extends('layouts.app')

@section('title', 'Create Stock In')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Create Stock In</h1>
            <a href="{{ route('stock-in.index') }}" class="text-gray-600 hover:text-gray-800">Back to Stock In</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            

            <form method="POST" action="{{ route('stock-in.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="item_id" class="block text-gray-700 text-sm font-bold mb-2">Item Name</label>
                    <select name="item_id" id="item_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create Stock In</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection