<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('task_name');
            $table->string('task_name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('priority', ['high', 'medium', 'normal', 'low'])->default('normal');
            $table->enum('status', ['not_started', 'in_progress', 'awaiting_approval', 'completed', 'blocked'])->default('not_started');
            $table->dateTime('due_date')->nullable();
            $table->json('checklist_items')->nullable();
            $table->json('attachments')->nullable();
            $table->text('flag_reason')->nullable();
            $table->timestamps();
            $table->index(['assignee_id', 'status']);
            $table->index(['due_date']);
        });

        Schema::create('delegation_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delegation_comments');
        Schema::dropIfExists('delegations');
    }
};
