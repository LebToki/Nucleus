<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('raw_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('lot_box_code')->default('GCC-BLR-01');
            $table->integer('item_index');
            $table->string('variety_cut');
            $table->string('supplier_grade');
            $table->string('tier_class');
            $table->decimal('weight_kg', 8, 4);
            $table->decimal('base_rate_aed', 10, 2);
            $table->decimal('landed_cost_aed', 10, 2);
            $table->decimal('margin_pct', 5, 2)->default(40.00);
            $table->decimal('selling_price_per_kg', 10, 2);
            $table->timestamps();
        });

        Schema::create('product_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('display_name_en');
            $table->string('display_name_ar');
            $table->text('subtext')->nullable();
            $table->unsignedBigInteger('teaser_source_tier_id')->nullable();
            $table->decimal('teaser_grams', 5, 2)->default(5.00);
            $table->string('teaser_label')->nullable();
            $table->decimal('margin_target_pct', 5, 2);
            $table->timestamps();
        });

        Schema::create('sale_package_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_tier_id')->constrained('product_tiers');
            $table->string('package_size');
            $table->decimal('weight_sold_grams', 8, 2);
            $table->decimal('teaser_deducted_grams', 8, 2)->default(5.00);
            $table->unsignedBigInteger('teaser_deducted_tier_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_package_allocations');
        Schema::dropIfExists('product_tiers');
        Schema::dropIfExists('raw_inventory_items');
    }
};
