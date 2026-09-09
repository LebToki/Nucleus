<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['meeting', 'call', 'site_visit', 'tasting', 'showcase', 'other'])->default('meeting');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->string('host_name')->nullable();
            $table->string('host_name_ar')->nullable();
            $table->string('guest_of_honor')->nullable();
            $table->string('guest_of_honor_ar')->nullable();
            $table->text('protocol_notes')->nullable();
            $table->text('protocol_notes_ar')->nullable();
            $table->string('dietary_restrictions')->nullable();
            $table->string('dietary_restrictions_ar')->nullable();
            $table->json('required_materials')->nullable();
            $table->json('checklist_items')->nullable();
            $table->enum('priority', ['high', 'medium', 'normal', 'low'])->default('normal');
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->boolean('prep_done')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'start_time']);
            $table->index(['customer_id', 'start_time']);
            $table->index(['status', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
