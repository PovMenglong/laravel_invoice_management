<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->q, fn($query, $term) => $query->where(function ($w) use ($term) {
                $w->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            }))
            ->withCount('invoices')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create', ['customer' => new Customer]);
    }

    public function store(CustomerRequest $request)
    {
        Customer::create($request->validated());

        return redirect()->route('customers.index')->with('status', 'Customer created.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('customers.index')->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->exists()) {
            return back()->withErrors(['delete' => 'This customer has invoices and cannot be deleted.']);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Customer deleted.');
    }
}
