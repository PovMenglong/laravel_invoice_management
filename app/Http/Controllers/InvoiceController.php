<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        dd($request->all());   // temporary: shows exactly what the form sends
    }

    public function edit(Invoice $invoice)
    {
        abort_unless($invoice->status === 'draft', 403, 'Only draft invoices can be edited.');

        return view('invoices.edit', [
            'invoice'   => $invoice,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'products'  => Product::orderBy('name')->get(['id', 'name', 'price']),
            'items'     => $invoice->items
                ->map(fn($i) => $i->only(['product_id', 'description', 'qty', 'unit_price']))
                ->values(),
        ]);
    }
}
