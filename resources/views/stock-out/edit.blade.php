@extends('layouts.app')

@section('title', 'Edit Stock Out')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Stock Out</h1>
            <a href="{{ route('stock-out.index') }}" class="text-gray-600 hover:text-gray-800">Back to Stock Out</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('stock-out.update', $stockOut) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="item_id" class="block text-gray-700 text-sm font-bold mb-2">Product</label>
                    <select name="item_id" id="item_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('item_id') border-red-500 @enderror">
                        <option value="">Select Product</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-price="{{ $item->price }}" {{ old('item_id', $stockOut->item_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} (Stock: {{ $item->stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $stockOut->quantity) }}" required min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror"
                        placeholder="0">
                    @error('quantity')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="Consumed" {{ old('status', $stockOut->status) == 'Consumed' ? 'selected' : '' }}>Consumed</option>
                        <option value="Damaged" {{ old('status', $stockOut->status) == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Unit Price (Rp)</label>
                    <input type="text" id="unit_price_display" readonly
                        class="w-full px-3 py-2 border border-gray-200 bg-gray-50 text-gray-500 rounded-md cursor-not-allowed outline-none"
                        placeholder="0">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Total Price (Rp)</label>
                    <input type="text" id="total_price_display" readonly
                        class="w-full px-3 py-2 border border-gray-200 bg-gray-50 text-gray-800 font-bold rounded-md cursor-not-allowed outline-none"
                        placeholder="0">
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                        placeholder="Additional information...">{{ old('notes', $stockOut->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Stock Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const itemSelect = document.getElementById('item_id');
    const quantityInput = document.getElementById('quantity');
    const unitPriceDisplay = document.getElementById('unit_price_display');
    const totalPriceDisplay = document.getElementById('total_price_display');

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function calculateTotal() {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const price = selectedOption ? parseFloat(selectedOption.dataset.price) || 0 : 0;
        const quantity = parseInt(quantityInput.value) || 0;
        
        unitPriceDisplay.value = formatNumber(price);
        totalPriceDisplay.value = formatNumber(price * quantity);
    }

    itemSelect.addEventListener('change', calculateTotal);
    quantityInput.addEventListener('input', calculateTotal);

    // Initial calculation
    calculateTotal();
</script>
@endpush
@endsection
