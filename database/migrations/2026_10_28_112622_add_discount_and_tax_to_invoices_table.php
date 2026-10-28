<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('discount_type', 10)->default('fixed')->after('subtotal');   // fixed | percent
            $table->decimal('discount_value', 12, 2)->default(0)->after('discount_type'); // what the user typed
            $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_value'); // computed money
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');      // e.g. 10.00
            $table->decimal('tax_amount', 12, 2)->default(0)->after('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_amount', 'tax_rate', 'tax_amount']);
        });
    }
};
