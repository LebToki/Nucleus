<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_lots', function (Blueprint $table) {
            $table->id();
            $table->string('source_reference');
            $table->date('source_date')->nullable();
            $table->string('distributor_shape');
            $table->string('business_name');
            $table->string('business_name_ar');
            $table->string('grade');
            $table->decimal('quantity_kg', 10, 4);
            $table->decimal('supplier_rate_per_kg', 12, 2);
            $table->decimal('supplier_amount', 12, 2);
            $table->decimal('working_cost_rate', 5, 4)->default(0.1250);
            $table->decimal('target_margin_rate', 5, 4)->default(0.2500);
            $table->timestamps();

            $table->index(['business_name', 'grade']);
            $table->index('distributor_shape');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_lots');
    }
};
