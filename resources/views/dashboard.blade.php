@php $money = fn ($n) => config('company.currency') . number_format($n, 2); @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
            <a href="{{ route('invoices.create') }}"
               class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white">+ New invoice</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('invoices.index', ['status' => 'issued']) }}" class="rounded-lg bg-white p-5 shadow">
                    <div class="text-xs uppercase text-gray-500">Outstanding</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $money($outstanding) }}</div>
                    <div class="text-xs text-gray-500">{{ $outstandingN }} issued invoice(s)</div>
                </a>

                <a href="{{ route('invoices.index', ['status' => 'overdue']) }}" class="rounded-lg bg-white p-5 shadow">
                    <div class="text-xs uppercase text-red-600">Overdue</div>
                    <div class="mt-1 text-2xl font-semibold text-red-600">{{ $money($overdue->amount) }}</div>
                    <div class="text-xs text-gray-500">{{ $overdue->n }} invoice(s) past due date</div>
                </a>

                <a href="{{ route('invoices.index', ['status' => 'paid']) }}" class="rounded-lg bg-white p-5 shadow">
                    <div class="text-xs uppercase text-green-600">Paid this month</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600">{{ $money($paidThisMonth) }}</div>
                    <div class="text-xs text-gray-500">{{ now()->format('F Y') }}</div>
                </a>

                <a href="{{ route('invoices.index', ['status' => 'draft']) }}" class="rounded-lg bg-white p-5 shadow">
                    <div class="text-xs uppercase text-gray-500">Drafts</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $drafts }}</div>
                    <div class="text-xs text-gray-500">not yet issued</div>
                </a>
            </div>

            <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
                <div class="flex items-center justify-between border-b p-4">
                    <h3 class="font-semibold">Recent invoices</h3>
                    <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600">View all</a>
                </div>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y">
                        @forelse ($recent as $invoice)
                            <tr>
                                <td class="p-3">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="font-medium text-indigo-600">{{ $invoice->number }}</a>
                                </td>
                                <td class="p-3">{{ $invoice->customer->name }}</td>
                                <td class="p-3"><x-invoice-status :invoice="$invoice" /></td>
                                <td class="p-3 text-right">{{ $money($invoice->total) }}</td>
                            </tr>
                        @empty
                            <tr><td class="p-6 text-center text-gray-500">No invoices yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
