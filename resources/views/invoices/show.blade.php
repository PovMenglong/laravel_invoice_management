@php

    $actions = [
        'issued' => ['Issue', 'Issue this invoice? It will be locked and can no longer be edited.'],
        'paid' => ['Mark as paid', 'Mark this invoice as paid?'],
        'void' => ['Void', 'Void this invoice? This cannot be undone.'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Invoice {{ $invoice->number }}
            <x-invoice-status :invoice="$invoice" class="ml-2" />
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            @error('transition')
                <div class="rounded bg-red-100 p-3 text-sm text-red-800">{{ $message }}</div>
            @enderror

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2">
                @if ($invoice->isDraft())
                    <a href="{{ route('invoices.edit', $invoice) }}" class="rounded-md border px-4 py-2 text-sm">Edit</a>
                    <a href="{{ route('invoices.print', $invoice) }}" target="_blank" rel="noopener"
                        class="rounded-md border px-4 py-2 text-sm">Print</a>
                @endif

                @foreach ($actions as $to => [$label, $confirm])
                    @if ($invoice->canMoveTo($to))
                        <form method="POST" action="{{ route('invoices.status', $invoice) }}"
                            onsubmit="return confirm('{{ $confirm }}')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $to }}">
                            <button
                                class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white">{{ $label }}</button>
                        </form>
                    @endif
                @endforeach

                @if ($invoice->isDraft())
                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}"
                        onsubmit="return confirm('Delete this draft?')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-md px-4 py-2 text-sm text-red-600">Delete draft</button>
                    </form>
                @endif
            </div>

            {{-- Invoice body --}}
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <div class="text-xs uppercase text-gray-500">Bill to</div>
                        <div class="font-semibold">{{ $invoice->customer->name }}</div>
                        <div class="text-sm text-gray-600 whitespace-pre-line">{{ $invoice->customer->address }}</div>
                        <div class="text-sm text-gray-600">{{ $invoice->customer->phone }}</div>
                    </div>
                    <div class="text-sm sm:text-right">
                        <div>Issue date: {{ $invoice->issue_date->format('d M Y') }}</div>
                        <div>Due date: {{ $invoice->due_date?->format('d M Y') ?? '-' }}</div>
                        <div>Created by: {{ $invoice->user->name }}</div>
                        @if ($invoice->paid_at)
                            <div>Paid on: {{ $invoice->paid_at->format('d M Y') }}</div>
                        @endif
                    </div>
                </div>

                <table class="mt-6 w-full text-sm">
                    <thead class="border-b text-left text-gray-600">
                        <tr>
                            <th class="py-2">Description</th>
                            <th class="py-2 text-right">Qty</th>
                            <th class="py-2 text-right">Unit price</th>
                            <th class="py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->description }}</td>
                                <td class="py-2 text-right">{{ number_format($item->qty, 2) }}</td>
                                <td class="py-2 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-2 text-right">{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t">
                            <td colspan="3" class="py-2 text-right">Subtotal</td>
                            <td class="py-2 text-right">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if ($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="py-2 text-right">
                                    Discount{{ $invoice->discount_type === 'percent' ? ' (' . rtrim(rtrim($invoice->discount_value, '0'), '.') . '%)' : '' }}
                                </td>
                                <td class="py-2 text-right">-{{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->tax_rate > 0)
                            <tr>
                                <td colspan="3" class="py-2 text-right">Tax
                                    ({{ rtrim(rtrim($invoice->tax_rate, '0'), '.') }}%)</td>
                                <td class="py-2 text-right">{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="border-t font-semibold">
                            <td colspan="3" class="py-3 text-right">Total</td>
                            <td class="py-3 text-right">{{ number_format($invoice->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                @if ($invoice->notes)
                    <div class="mt-6 text-sm text-gray-600 whitespace-pre-line">{{ $invoice->notes }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
