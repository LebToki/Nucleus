<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('personal_phone')->nullable()->after('email');
            $table->string('corporate_phone')->nullable()->after('personal_phone');
            $table->string('secondary_phone')->nullable()->after('corporate_phone');
            $table->string('personal_email')->nullable()->after('email');
            $table->string('corporate_email')->nullable()->after('personal_email');
            $table->string('secondary_email')->nullable()->after('corporate_email');
            $table->string('personal_website')->nullable()->after('corporate_email');
            $table->string('corporate_website')->nullable()->after('personal_website');
            $table->string('secondary_website')->nullable()->after('corporate_website');
            $table->string('linkedin')->nullable()->after('secondary_website');
            $table->string('facebook')->nullable()->after('linkedin');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('x')->nullable()->after('instagram');
            $table->string('tiktok')->nullable()->after('x');
            $table->string('youtube')->nullable()->after('tiktok');
            $table->string('avatar')->nullable()->after('youtube');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'personal_phone',
                'corporate_phone',
                'secondary_phone',
                'personal_email',
                'corporate_email',
                'secondary_email',
                'personal_website',
                'corporate_website',
                'secondary_website',
                'linkedin',
                'facebook',
                'instagram',
                'x',
                'tiktok',
                'youtube',
                'avatar',
            ]);
        });
    }
};
