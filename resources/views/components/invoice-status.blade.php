@props(['invoice'])

@php
    $badge = [
        'draft'  => 'bg-gray-100 text-gray-700',
        'issued' => 'bg-blue-100 text-blue-700',
        'paid'   => 'bg-green-100 text-green-700',
        'void'   => 'bg-red-100 text-red-700',
    ][$invoice->status];
@endphp

<span class="rounded-full px-2 py-1 text-xs font-medium {{ $badge }}">{{ ucfirst($invoice->status) }}</span>
@if ($invoice->isOverdue())
    <span class="ml-1 rounded-full bg-orange-100 px-2 py-1 text-xs font-medium text-orange-700">Overdue</span>
@endif
