<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function create()
    {
        return view('invoices.create', [
            'invoice'   => new Invoice(['issue_date' => today()]),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'products'  => Product::orderBy('name')->get(['id', 'name', 'price']),
            'items'     => [['product_id' => '', 'description' => '', 'qty' => 1, 'unit_price' => 0]],
        ]);
    }

    public function store(InvoiceRequest $request)
    {
        $invoice = DB::transaction(function () use ($request) {
            $invoice = Invoice::create([
                ...$request->safe()->only(['customer_id', 'issue_date', 'due_date', 'notes']),
                'number'  => $this->nextNumber(),
                'user_id' => $request->user()->id,
                'status'  => 'draft',
            ]);

            $this->syncItems($invoice, $request->validated('items'));

            return $invoice;
        });

        return redirect()->route('invoices.edit', $invoice)
            ->with('status', "Invoice {$invoice->number} saved.");
    }

    public function edit(Invoice $invoice)
    {
        abort_unless($invoice->status === 'draft', 403, 'Only draft invoices can be edited.');

        return view('invoices.edit', [
            'invoice'   => $invoice,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'products'  => Product::orderBy('name')->get(['id', 'name', 'price']),
            'items'     => $invoice->items
                ->map(fn ($i) => $i->only(['product_id', 'description', 'qty', 'unit_price']))
                ->values(),
        ]);
    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        abort_unless($invoice->status === 'draft', 403, 'Only draft invoices can be edited.');

        DB::transaction(function () use ($request, $invoice) {
            $invoice->update($request->safe()->only(['customer_id', 'issue_date', 'due_date', 'notes']));
            $this->syncItems($invoice, $request->validated('items'));
        });

        return redirect()->route('invoices.edit', $invoice)
            ->with('status', 'Invoice updated.');
    }

    /** Replace the invoice's lines and recalculate every amount on the server. */
    private function syncItems(Invoice $invoice, array $rows): void
    {
        $invoice->items()->delete();

        $subtotal = 0;

        foreach ($rows as $row) {
            $lineTotal = round($row['qty'] * $row['unit_price'], 2);

            $invoice->items()->create([
                'product_id'  => $row['product_id'] ?? null,
                'description' => $row['description'],
                'qty'         => $row['qty'],
                'unit_price'  => $row['unit_price'],
                'line_total'  => $lineTotal,
            ]);

            $subtotal += $lineTotal;
        }

        $subtotal = round($subtotal, 2);
        $invoice->update(['subtotal' => $subtotal, 'total' => $subtotal]);
    }

    /** INV-2026-0001, INV-2026-0002, ... restarting each year. */
    private function nextNumber(): string
    {
        $prefix = 'INV-' . now()->format('Y') . '-';

        $last = Invoice::where('number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('number');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
