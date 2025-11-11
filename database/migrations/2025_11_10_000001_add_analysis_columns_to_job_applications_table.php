<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table): void {
            $table->json('role_analysis')->nullable()->after('notes');
            $table->json('profile_matching')->nullable()->after('role_analysis');
            $table->json('cover_letter')->nullable()->after('profile_matching');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table): void {
            $table->dropColumn([
                'role_analysis',
                'profile_matching',
                'cover_letter',
            ]);
        });
    }
};
