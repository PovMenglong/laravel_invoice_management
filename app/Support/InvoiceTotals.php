<?php

namespace App\Support;

class InvoiceTotals
{
    /**
     * @param  float[]  $lineTotals  already-rounded line amounts
     * @param  string   $discountType  'fixed' or 'percent'
     */
    public static function calculate(array $lineTotals, string $discountType, float $discountValue, float $taxRate): array
    {
        $subtotal = round(array_sum($lineTotals), 2);

        $discount = $discountType === 'percent'
            ? round($subtotal * $discountValue / 100, 2)
            : round(min($discountValue, $subtotal), 2);     // a discount can never exceed the subtotal

        $taxable = round($subtotal - $discount, 2);
        $tax     = round($taxable * $taxRate / 100, 2);

        return [
            'subtotal'        => $subtotal,
            'discount_amount' => $discount,
            'tax_amount'      => $tax,
            'total'           => round($taxable + $tax, 2),
        ];
    }
}
