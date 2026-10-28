@php $money = fn ($n) => config('company.currency') . number_format($n, 2); @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Products</h2>
            <a href="{{ route('products.create') }}"
               class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white">+ New product</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <form method="GET" class="flex gap-2">
                <x-text-input name="q" :value="request('q')" placeholder="Search product name" class="w-64" />
                <x-secondary-button type="submit">Search</x-secondary-button>
            </form>

            <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3 text-right">Price</th>
                            <th class="p-3 text-right">Used on invoice lines</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($products as $product)
                            <tr>
                                <td class="p-3">{{ $product->name }}</td>
                                <td class="p-3 text-right">{{ $money($product->price) }}</td>
                                <td class="p-3 text-right">{{ $product->invoice_items_count }}</td>
                                <td class="p-3 text-right space-x-3">
                                    <a href="{{ route('products.edit', $product) }}" class="text-indigo-600">Edit</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                          class="inline" onsubmit="return confirm('Delete this product? Existing invoices keep their lines.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-6 text-center text-gray-500">No products yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
