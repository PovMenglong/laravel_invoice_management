@csrf

@if (session('status'))
    <div class="mb-4 rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded bg-red-100 p-3 text-sm text-red-800">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <x-input-label for="customer_id" value="Customer" />
        <select id="customer_id" name="customer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Select customer…</option>
            @foreach ($customers as $c)
                <option value="{{ $c->id }}" @selected(old('customer_id', $invoice->customer_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="issue_date" value="Issue date" />
        <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full"
                      :value="old('issue_date', $invoice->issue_date?->format('Y-m-d'))" required />
    </div>
    <div>
        <x-input-label for="due_date" value="Due date" />
        <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
                      :value="old('due_date', $invoice->due_date?->format('Y-m-d'))" />
    </div>
</div>

<div class="mt-6"
     x-data="{
        items: @js(old('items', $items)),
        products: @js($products),
        discountType: @js(old('discount_type', $invoice->discount_type ?? 'fixed')),
        discountValue: @js(old('discount_value', $invoice->discount_value ?? 0)),
        taxRate: @js(old('tax_rate', $invoice->tax_rate ?? 0)),

        addLine() { this.items.push({ product_id: '', description: '', qty: 1, unit_price: 0 }) },
        removeLine(i) { this.items.splice(i, 1) },
        pick(i) {
            const p = this.products.find(p => p.id == this.items[i].product_id)
            if (p) { this.items[i].description = p.name; this.items[i].unit_price = p.price }
        },

        round(n) { return Math.round((n + Number.EPSILON) * 100) / 100 },
        lineTotal(it) { return this.round((Number(it.qty) || 0) * (Number(it.unit_price) || 0)) },
        get subtotal() { return this.round(this.items.reduce((s, it) => s + this.lineTotal(it), 0)) },
        get discount() {
            const v = Number(this.discountValue) || 0
            return this.round(this.discountType === 'percent' ? this.subtotal * v / 100 : Math.min(v, this.subtotal))
        },
        get tax() { return this.round((this.subtotal - this.discount) * (Number(this.taxRate) || 0) / 100) },
        get grandTotal() { return this.round(this.subtotal - this.discount + this.tax) },
        money(n) { return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }
     }">

    <table class="w-full text-sm">
        <thead class="text-left text-gray-600">
            <tr>
                <th class="w-48 p-2">Product</th>
                <th class="p-2">Description</th>
                <th class="w-24 p-2">Qty</th>
                <th class="w-32 p-2">Unit price</th>
                <th class="w-32 p-2 text-right">Line total</th>
                <th class="w-10"></th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(item, i) in items" :key="i">
                <tr class="border-t">
                    <td class="p-2">
                        <select :name="`items[${i}][product_id]`" x-model="item.product_id" @change="pick(i)"
                                class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Custom item</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-2">
                        <input type="text" :name="`items[${i}][description]`" x-model="item.description" required
                               class="w-full rounded-md border-gray-300 text-sm">
                    </td>
                    <td class="p-2">
                        <input type="number" step="0.01" min="0.01" :name="`items[${i}][qty]`" x-model.number="item.qty" required
                               class="w-full rounded-md border-gray-300 text-sm">
                    </td>
                    <td class="p-2">
                        <input type="number" step="0.01" min="0" :name="`items[${i}][unit_price]`" x-model.number="item.unit_price" required
                               class="w-full rounded-md border-gray-300 text-sm">
                    </td>
                    <td class="p-2 text-right" x-text="money(lineTotal(item))"></td>
                    <td class="p-2 text-center">
                        <button type="button" @click="removeLine(i)" x-show="items.length > 1" class="text-red-600">✕</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <button type="button" @click="addLine()" class="mt-3 text-sm text-indigo-600">+ Add line</button>

    <div class="mt-6 ml-auto w-full max-w-sm space-y-2 text-sm">
        <div class="flex justify-between">
            <span>Subtotal</span>
            <span x-text="money(subtotal)"></span>
        </div>

        <div class="flex items-center justify-between gap-2">
            <span>Discount</span>
            <span class="flex gap-1">
                <select name="discount_type" x-model="discountType" class="rounded-md border-gray-300 text-sm">
                    <option value="fixed">Amount</option>
                    <option value="percent">%</option>
                </select>
                <input type="number" step="0.01" min="0" name="discount_value" x-model.number="discountValue"
                       class="w-24 rounded-md border-gray-300 text-sm">
            </span>
            <span x-text="'-' + money(discount)"></span>
        </div>

        <div class="flex items-center justify-between gap-2">
            <span>Tax rate (%)</span>
            <input type="number" step="0.01" min="0" max="100" name="tax_rate" x-model.number="taxRate"
                   class="w-24 rounded-md border-gray-300 text-sm">
            <span x-text="money(tax)"></span>
        </div>

        <div class="flex justify-between border-t pt-2 text-lg font-semibold">
            <span>Total</span>
            <span x-text="money(grandTotal)"></span>
        </div>
    </div>
</div>

<div class="mt-6">
    <x-input-label for="notes" value="Notes" />
    <textarea id="notes" name="notes" rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $invoice->notes) }}</textarea>
</div>
