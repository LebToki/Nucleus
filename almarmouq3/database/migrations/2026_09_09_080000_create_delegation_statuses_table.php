<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delegation_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('color')->default('neutral');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        $statuses = [
            ['code' => 'not_started', 'name_en' => 'Not Started', 'name_ar' => 'لم يبدأ', 'color' => 'neutral', 'sort_order' => 0, 'is_closed' => false],
            ['code' => 'in_progress', 'name_en' => 'In Progress', 'name_ar' => 'قيد التقدم', 'color' => 'warning', 'sort_order' => 1, 'is_closed' => false],
            ['code' => 'awaiting_approval', 'name_en' => 'Awaiting Your Approval', 'name_ar' => 'في انتظار موافقتك', 'color' => 'primary', 'sort_order' => 2, 'is_closed' => false],
            ['code' => 'blocked', 'name_en' => 'Blocked', 'name_ar' => 'معلق', 'color' => 'danger', 'sort_order' => 3, 'is_closed' => false],
            ['code' => 'completed', 'name_en' => 'Completed', 'name_ar' => 'مكتمل', 'color' => 'success', 'sort_order' => 4, 'is_closed' => true],
        ];

        foreach ($statuses as $s) {
            DB::table('delegation_statuses')->insert(array_merge($s, ['created_at' => now(), 'updated_at' => now()]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delegation_statuses');
    }
};
