<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- Card Start --}}
                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center p-6 bg-gray-300">
                            <h4 class="card-title">All Sales</h4>
                            <div class="flex items-center">
                                <a href="{{ route('sales.create') }}"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create
                                    Sale</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="p-6 text-gray-900">

                            <!-- Sales Table -->
                            <table class="min-w-full border divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #</th>
                                        <th class="px-6 py-3 bg-gray-50">Customer</th>
                                        <th class="px-6 py-3 bg-gray-50">Products</th>
                                        <th class="px-6 py-3 bg-gray-50">Amount</th>
                                        <th class="px-6 py-3 bg-gray-50">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($sales as $key => $sale)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $sales->firstItem() + $key }}
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                {{ $sale->customer->name }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                {{ $sale->products->pluck('name')->join(', ') }}
                                            </td>
                                            <td class="px-6 py-4 text-center">{{ $sale->total_amount }}</td>
                                            <td class="px-6 py-4 text-center">{{ $sale->created_at->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <x-pagination :paginator="$sales" />
                </div>
                {{-- Card End --}}
            </div>
        </div>
    </div>
</x-app-layout>
