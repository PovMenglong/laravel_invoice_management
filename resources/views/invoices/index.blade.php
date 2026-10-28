@php $money = fn ($n) => config('company.currency') . number_format($n, 2); @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Invoices</h2>
            <a href="{{ route('invoices.create') }}"
               class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white">+ New invoice</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded bg-red-100 p-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <form method="GET" class="flex flex-wrap items-end gap-3 rounded-lg bg-white p-4 shadow">
                <div>
                    <x-input-label for="q" value="Search" />
                    <x-text-input id="q" name="q" :value="request('q')" placeholder="Number or customer" class="mt-1 w-56" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">All</option>
                        @foreach (['draft' => 'Draft', 'issued' => 'Issued', 'overdue' => 'Overdue', 'paid' => 'Paid', 'void' => 'Void'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="from" value="From" />
                    <x-text-input id="from" name="from" type="date" :value="request('from')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="to" value="To" />
                    <x-text-input id="to" name="to" type="date" :value="request('to')" class="mt-1" />
                </div>
                <x-primary-button>Filter</x-primary-button>
                <a href="{{ route('invoices.index') }}" class="text-sm text-gray-600 underline">Reset</a>
            </form>

            <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-3">Number</th>
                            <th class="p-3">Customer</th>
                            <th class="p-3">Issue date</th>
                            <th class="p-3">Due date</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td class="p-3">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="font-medium text-indigo-600">{{ $invoice->number }}</a>
                                </td>
                                <td class="p-3">{{ $invoice->customer->name }}</td>
                                <td class="p-3">{{ $invoice->issue_date->format('d M Y') }}</td>
                                <td class="p-3">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</td>
                                <td class="p-3"><x-invoice-status :invoice="$invoice" /></td>
                                <td class="p-3 text-right">{{ $money($invoice->total) }}</td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('invoices.print', $invoice) }}" target="_blank" rel="noopener" class="text-gray-600">Print</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-gray-500">No invoices found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="text-xs text-gray-500">{{ $invoices->total() }} invoice(s)</div>
            {{ $invoices->links() }}
        </div>
    </div>
</x-app-layout>
