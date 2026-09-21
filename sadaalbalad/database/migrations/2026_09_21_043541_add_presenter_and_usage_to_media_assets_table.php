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
        Schema::table('media_assets', function (Blueprint $table) {
            $table->foreignId('presenter_id')->nullable()->after('episode_id')->constrained()->nullOnDelete();
            $table->string('usage', 60)->nullable()->after('presenter_id');

            $table->index(['presenter_id', 'is_available']);
            $table->index('usage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_assets', function (Blueprint $table) {
            $table->dropForeign(['presenter_id']);
            $table->dropIndex(['presenter_id', 'is_available']);
            $table->dropIndex(['usage']);
            $table->dropColumn(['presenter_id', 'usage']);
        });
    }
};
