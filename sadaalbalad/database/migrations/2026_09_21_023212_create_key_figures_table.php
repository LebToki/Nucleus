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
        if (Schema::hasTable('key_figures')) {
            return;
        }

        Schema::create('key_figures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('key_figures')) {
            return;
        }

        // This table is also referenced by FK constraints from child tables
        // created in earlier migrations (key_figure_roles, key_figure_photos,
        // key_figure_biographies, key_figure_articles, key_figure_fact_checks).
        // Those child tables are dropped by their own migrations later in the
        // rollback, so we must temporarily disable FK checks here to drop the
        // parent table without error.
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('key_figures');
        Schema::enableForeignKeyConstraints();
    }
};
