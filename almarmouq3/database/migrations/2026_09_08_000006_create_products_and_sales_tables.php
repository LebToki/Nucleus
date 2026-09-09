<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_key')->unique();
            $table->string('business_name');
            $table->string('business_name_ar');
            $table->string('grade');
            $table->string('distributor_shape');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['business_name', 'grade']);
        });

        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('package_name');
            $table->decimal('weight_kg', 10, 6);
            $table->decimal('unit_price', 12, 2);
            $table->string('currency', 3)->default('AED');
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->string('source')->default('manual');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['product_id', 'package_name', 'valid_from']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('b2c');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('trn')->nullable();
            $table->string('credit_terms')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['type', 'name']);
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'sold_at']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_lot_id')->nullable()->constrained('product_lots')->nullOnDelete();
            $table->string('package_name');
            $table->decimal('quantity_kg', 10, 6);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });

        Schema::table('product_lots', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_lots', fn(Blueprint $table) => $table->dropForeign(['product_id']));
        Schema::table('product_lots', fn(Blueprint $table) => $table->dropColumn('product_id'));
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('product_price_histories');
        Schema::dropIfExists('products');
    }
};
