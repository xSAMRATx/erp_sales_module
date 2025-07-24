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
                                        <th class="px-6 py-3 bg-gray-50">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($sales as $key => $sale)
                                        <tr id="row-{{ $sale->id }}">
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

                                            <td class="px-6 py-4 text-center">
                                                <button type="button" class="text-red-500 hover:text-gray-700"
                                                    onclick="simpleResourceDelete({{ $sale->id }}, '{{ route('sales.destroy', $sale->id) }}')"
                                                    title="Delete">
                                                    <i class="fa-solid fa-trash-can text-lg"></i>
                                                </button>


                                                <a href="{{ route('sales.show', $sale->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 mr-2" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('sales.edit', $sale->id) }}"
                                                    class="text-green-600 hover:text-green-800 mr-2" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this sale?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                                        title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form> --}}

                                                <button class="btn-delete"
                                                    data-id="{{ $sale->id }}">Delete</button>
                                            </td>

                                            {{-- <td class="px-6 py-4 text-center">
                                                <a href="#" class="text-blue-600 hover:text-blue-800 mr-2"><i
                                                        class="fas fa-eye"></i></a>
                                                <a href="#" class="text-green-600 hover:text-green-800 mr-2"><i
                                                        class="fas fa-edit"></i></a>
                                                <button class="text-red-600 hover:text-red-800"><i
                                                        class="fas fa-trash-alt"></i></button>
                                            </td> --}}

                                            {{-- <td class="px-6 py-4 text-center">
                                                <a href="{{ route('sales.show', $sale->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">View</a> |
                                                <a href="{{ route('sales.edit', $sale->id) }}"
                                                    class="text-yellow-500 hover:text-yellow-700">Edit</a> |
                                                <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this sale?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            </td> --}}
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

<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', async () => {
            const id = button.dataset.id;

            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                    const response = await axios.delete(`/sales/${id}`);

                    if (response.data.success) {
                        Swal.fire("Deleted!", "The sale has been deleted.", "success");
                        document.getElementById(`row-${id}`)
                            ?.remove(); // Right after success message
                    }

                    // if (response.data.success) {
                    //     Swal.fire('Deleted!', 'User has been deleted.', 'success').then(() => {
                    //         document.getElementById(`row-${id}`)?.remove();
                    //     });
                    // }


                    // if (response.data.success) {
                    //     Swal.fire('Deleted!', 'User has been deleted.', 'success');

                    //     location.reload();
                    // }

                    // if (response.data.success) {
                    //     Swal.fire('Deleted!', 'User has been deleted.', 'success').then(() => {
                    //         location.reload();
                    //     });
                    // }


                    // Remove the row
                    // const row = document.getElementById(`user-row-${id}`);
                    // if (row) row.remove();
                } catch (error) {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            }
        });
    });

    // Delete button click
    // document.querySelectorAll('.btn-delete').forEach(button => {
    //     button.addEventListener('click', () => {
    //         const id = button.dataset.id;

    //         Swal.fire({
    //             title: 'Are you sure, you want to delete this record?',
    //             text: "This action cannot be undone!",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#d33',
    //             cancelButtonColor: '#3085d6',
    //             confirmButtonText: 'Yes, delete it!'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 axios.delete(`/sales/${id}`)
    //                     .then(() => {
    //                         Swal.fire('Deleted!', 'The record has been deleted.',
    //                             'success');

    //                         // Optional: remove the deleted row from the DOM
    //                         const row = document.getElementById(`user-row-${id}`);
    //                         if (row) row.remove();
    //                     })
    //                     .catch(() => {
    //                         Swal.fire('Error!', 'Something went wrong.', 'error');
    //                     });
    //             }
    //         });
    //     });
    // });

    // Delete button logic
    // document.addEventListener('DOMContentLoaded', () => {
    //     document.querySelectorAll('.btn-delete').forEach(button => {
    //         button.addEventListener('click', () => {
    //             const id = button.dataset.id;

    //             Swal.fire({
    //                 title: 'Are you sure?',
    //                 text: "This action cannot be undone!",
    //                 icon: 'warning',
    //                 showCancelButton: true,
    //                 confirmButtonColor: '#d33',
    //                 cancelButtonColor: '#3085d6',
    //                 confirmButtonText: 'Yes, delete it!'
    //             }).then((result) => {
    //                 if (result.isConfirmed) {
    //                     window.axios.delete(`/sales/${id}`)
    //                         .then(() => {
    //                             Swal.fire('Deleted!', 'User has been deleted.',
    //                                 'success');

    //                             // Optional: remove row
    //                             const row = document.getElementById(
    //                                 `user-row-${id}`);
    //                             if (row) row.remove();
    //                         })
    //                         .catch(() => {
    //                             Swal.fire('Error!', 'Something went wrong.',
    //                                 'error');
    //                         });
    //                 }
    //             });
    //         });
    //     });
    // });
</script>
