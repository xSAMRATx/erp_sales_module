<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Sale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                {{-- Card Start --}}
                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center p-6 bg-gray-300">
                            <h4 class="card-title">Edit Sale Form</h4>
                            <div class="flex items-center">
                                <a href="{{ route('sales.index') }}"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Back</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <form id="edit_sale_form" action="{{ route('sales.update', $sale->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Customer -->
                            <div class="mb-4">
                                <label for="customer_id"
                                    class="block font-medium text-sm text-gray-700">Customer</label>
                                <select name="customer_id" id="customer_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('customer_id', $sale->customer_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>

                                <small id="customer_id-error"></small>
                            </div>

                            <!-- Product -->
                            <div class="mb-4">
                                <label for="product_id"
                                    class="block font-medium text-sm text-gray-700 mb-1">Product</label>
                                <select name="product_id[]" id="product_id" required multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    {{-- <option value="">Select Product</option> --}}
                                    @foreach ($products as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ collect(old('product_id', $sale->products->pluck('id') ?? []))->contains($id) ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>

                                <small id="product_id-error"></small>
                            </div>

                            <div id="solutionsTableContainer" class="col-span-2 mt-4 hidden mb-4">
                            </div>

                            <!-- Total Price -->
                            <div class="mb-4">
                                <label for="total_amount" class="block font-medium text-sm text-gray-700">Total
                                    Amount</label>
                                <input type="number" name="total_amount" id="total_amount"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('total_amount', $sale->total_amount ?? '') }}" step="0.01"
                                    min="0">
                            </div>

                            <!-- Sale Date -->
                            <div class="mb-4">
                                <label for="sale_date" class="block font-medium text-sm text-gray-700">Sale Date</label>
                                <input type="date" name="sale_date" id="sale_date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    value="{{ old('sale_date', isset($sale->sale_date) ? \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') : '') }}">

                                <small id="sale_date-error"></small>
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button id="submitBtn" type="submit"
                                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    onclick="storeOrUpdate('edit_sale_form', event)">
                                    Save
                                </button>

                                {{-- <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Save Sale
                        </button>
                        <a href="{{ route('sales.index') }}" class="ml-3 text-gray-600 hover:underline">Cancel</a> --}}
                            </div>
                        </form>
                    </div>
                </div>
                {{-- Card End --}}
            </div>
        </div>
    </div>
</x-app-layout>


<script>
    $('#product_id').select2({
        width: '100%',
        placeholder: "Select Product",
        multiple: true,
    });

    document.addEventListener('DOMContentLoaded', function() {

        console.log('DOM fully loaded and parsed');

        const saleId = @json($sale->id);

        console.log('Sale ID:', saleId);


        const productSelect = document.getElementById('product_id');
        const selectedSolutions = Array.from(productSelect.selectedOptions).map(option => option.value);

        const salesPriceInput = document.getElementById('total_amount');
        // const discountPercentageInput = document.getElementById('discount_percentage');
        const finalPriceInput = document.getElementById('total_amount');

        if (selectedSolutions.length > 0) {
            if (saleId) {
                fetchSolutionsData(selectedSolutions, saleId);
            } else {
                console.error('Quote ID is missing.');
            }
        }

        // if (selectedSolutions.length > 0) {
        //     fetchSolutionsData(selectedSolutions);
        // }

        $('#product_id').on('change', function() {
            const selectedIds = $(this).val();

            if (selectedIds.length > 0) {
                fetchSolutionsData(selectedIds, saleId);
            } else {
                $('#solutionsTableContainer').addClass('hidden').html('');

                updateTotalSalesPrice(0);
                calculateFinalPrice();
            }
        });

        function fetchSolutionsData(solutionIds, $saleId) {
            $.ajax({
                url: `/fetch/sale/${saleId}/products`,
                type: 'GET',
                data: {
                    productIds: solutionIds
                },
                success: function(products) {
                    const container = $('#solutionsTableContainer');
                    container.removeClass('hidden');
                    container.html('');

                    const table = `
                <table id="solutionsTable" class="table-auto border-collapse border border-gray-300 w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">#</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                            <th class="border border-gray-300 px-4 py-2">Price</th>
                            <th class="border border-gray-300 px-4 py-2">Quantity</th>
                            <th class="border border-gray-300 px-4 py-2">Discount Percentage</th>
                            <th class="border border-gray-300 px-4 py-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${products.map((product, index) => {
                            return `
                                                                        <tr data-solution-id="${product.id}" data-price="${product.price}">
                                                                            <td class="border border-gray-300 px-4 py-2 text-center">${index + 1}</td>
                                                                            <td class="border border-gray-300 px-4 py-2">${product.name}</td>
                                                                            <td class="border border-gray-300 px-4 py-2 text-center">${product.price}</td>
                                                                            <td class="border border-gray-300 px-4 py-2 text-center">
                                                                                <input type="number" name="quantity[${product.id}]" min="1"
                                                                                    class="quantity-input form-input w-full text-center"
                                                                                    value="${product.quantity ?? 1}">
                                                                            </td>
                                                                            <td class="border border-gray-300 px-4 py-2 text-center">
                                                                                <input type="number" name="discount[${product.id}]" min="0" max="100"
                                                                                    class="discount-percentage-input form-input w-full text-center"
                                                                                    value="${product.discount_percentage ?? 0}">
                                                                            </td>
                                                                            <td class="amount-cell border border-gray-300 px-4 py-2 text-center">${product.price}</td>
                                                                        </tr>`;
                        }).join('')}
                    </tbody>
                </table>
                `;

                    container.html(table);

                    attachInputListeners();
                    calculateTotalSalesPrice();
                    calculateFinalPrice();
                },
                error: function(error) {
                    console.error('Error fetching solutions:', error);
                }
            });
        }

        function attachInputListeners() {
            $('.quantity-input, .discount-percentage-input').on('input', function() {
                const row = $(this).closest('tr');
                const solutionId = row.data('solution-id');
                const price = parseFloat(row.data('price'));
                const quantity = parseInt(row.find('.quantity-input').val()) || 1;
                const discount = parseFloat(row.find('.discount-percentage-input').val()) ||
                    0;
                const amountCell = row.find('.amount-cell');

                const discountAmount = (price * discount) / 100;
                const amount = (price - discountAmount) * quantity;
                amountCell.text(amount.toFixed(2));

                calculateTotalSalesPrice();
                calculateFinalPrice();
            });
        }

        function calculateTotalSalesPrice() {
            let total = 0;
            $('.amount-cell').each(function() {
                total += parseFloat($(this).text()) || 0;
            });
            updateTotalSalesPrice(total);
        }

        function updateTotalSalesPrice(total) {
            salesPriceInput.value = total.toFixed(2);
        }

        function calculateFinalPrice() {
            const salesPrice = parseFloat(salesPriceInput.value) || 0;
            // const discountPercentage = parseFloat(discountPercentageInput.value) || 0;

            const discountPercentage = 0;

            const discountAmount = (salesPrice * discountPercentage) / 100;
            const finalPrice = salesPrice - discountAmount;

            finalPriceInput.value = finalPrice.toFixed(2);
        }

        // discountPercentageInput.addEventListener('input', calculateFinalPrice);
    });
</script>
