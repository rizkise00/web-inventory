@extends('layouts.app')

@section('title', 'Create Stock Masuk')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Create Stock Masuk</h1>
            <a href="{{ route('stock-in.index') }}" class="text-gray-600 hover:text-gray-800">Back to Stock Masuk</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('stock-in.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="item_name" class="block text-gray-700 text-sm font-bold mb-2">Item Name</label>
                    <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-4">
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-4">
                    <label for="supplier" class="block text-gray-700 text-sm font-bold mb-2">Supplier</label>
                    <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-4">
                    <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Create Stock Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
