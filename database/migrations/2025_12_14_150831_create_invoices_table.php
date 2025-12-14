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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained();
            $table->decimal('subtotal', 12, 2);        // total without tax
            $table->decimal('tax_total', 12, 2);       // total tax
            $table->decimal('grand_total', 12, 2);     // net price
            $table->decimal('rounded_total', 12, 2);   // rounded down net price
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('balance_returned', 12, 2);
            $table->enum('payment_mode', ['cash'])->default('cash');
            $table->timestamp('purchase_date')->index();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
