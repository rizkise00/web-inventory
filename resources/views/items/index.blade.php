@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Products</h1>
                <p class="text-gray-600">Manage product data</p>
            </div>
            <a href="{{ route('items.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Product
            </a>
        </div>

        
        <!-- Search and Actions -->
        <div class="mb-6">
            <form action="{{ route('items.index') }}" method="GET" class="space-y-3">
                <!-- Filter fields: 3 equal-width columns -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Product Name..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <select name="low_stock" class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm bg-white">
                            <option value="">All Stock Levels</option>
                            <option value="1"    {{ request('low_stock') == '1'    ? 'selected' : '' }}>Low Stock (&lt; 5)</option>
                            <option value="asc"  {{ request('low_stock') == 'asc'  ? 'selected' : '' }}>Lowest Stock</option>
                            <option value="desc" {{ request('low_stock') == 'desc' ? 'selected' : '' }}>Highest Stock</option>
                        </select>
                    </div>
                    <div class="relative flex-1">
                        <input type="hidden" name="date_from" id="date_from_items" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="date_to_items" value="{{ request('date_to') }}">
                        <input type="text" id="daterange_items" placeholder="Select date range..." class="w-full py-2 px-3 pl-9 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all text-gray-700 cursor-pointer bg-white" readonly>
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>
                <!-- Action buttons -->
                <div class="flex flex-wrap gap-2 items-center justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"></path></svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'low_stock', 'date_from', 'date_to']))
                    <a href="{{ route('items.index') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg text-sm transition-all duration-200">Reset</a>
                    @endif
                    <a href="{{ route('items.export', request()->only(['search', 'low_stock', 'date_from', 'date_to'])) }}" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-lg overflow-x-auto border border-green-100 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($items as $item)
                    <tr class="hover:bg-green-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $item->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $item->stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 text-sm">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('items.show', $item) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-3 rounded-lg text-xs shadow-md transition-all duration-200">View</a>
                                <a href="{{ route('items.edit', $item) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs shadow-md transition-all duration-200">Edit</a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1.5 px-3 rounded-lg text-xs shadow-md transition-all duration-200">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $items->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
// Flatpickr date range
(function() {
    const fp = flatpickr('#daterange_items', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        onChange: function(selectedDates) {
            document.getElementById('date_from_items').value = selectedDates.length >= 1 ? flatpickr.formatDate(selectedDates[0], 'Y-m-d') : '';
            document.getElementById('date_to_items').value   = selectedDates.length >= 2 ? flatpickr.formatDate(selectedDates[1], 'Y-m-d') : '';
        }
    });
    const df = document.getElementById('date_from_items').value;
    const dt = document.getElementById('date_to_items').value;
    if (df && dt) fp.setDate([df, dt]);
    else if (df) fp.setDate([df]);
})();

// Delete confirmation
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Delete Product?',
            text: 'Are you sure you want to delete this product? This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
