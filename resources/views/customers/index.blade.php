<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Customers</h2>
            <a href="{{ route('customers.create') }}"
               class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white">+ New customer</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div>
            @endif
            @error('delete')
                <div class="rounded bg-red-100 p-3 text-red-800">{{ $message }}</div>
            @enderror

            <form method="GET" class="flex gap-2">
                <x-text-input name="q" :value="request('q')" placeholder="Search name or phone" class="w-64" />
                <x-secondary-button type="submit">Search</x-secondary-button>
            </form>

            <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Phone</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Invoices</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="p-3">{{ $customer->name }}</td>
                                <td class="p-3">{{ $customer->phone }}</td>
                                <td class="p-3">{{ $customer->email }}</td>
                                <td class="p-3">{{ $customer->invoices_count }}</td>
                                <td class="p-3 text-right space-x-3">
                                    <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600">Edit</a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                          class="inline" onsubmit="return confirm('Delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">No customers yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $customers->links() }}
        </div>
    </div>
</x-app-layout>
