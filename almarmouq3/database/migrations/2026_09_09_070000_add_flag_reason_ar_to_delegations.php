<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('delegations', function (Blueprint $table) {
            $table->string('flag_reason_ar')->nullable()->after('flag_reason');
        });
    }

    public function down(): void
    {
        Schema::table('delegations', function (Blueprint $table) {
            $table->dropColumn('flag_reason_ar');
        });
    }
};
