<?php

declare(strict_types=1);

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
        // Remove analysis columns from documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['analysis', 'analyzed_at']);
        });

        // Remove analysis columns from job_applications table
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'role_analysis',
                'profile_matching',
                'cover_letter',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore analysis columns to documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->json('analysis')->nullable()->after('file_hash');
            $table->timestamp('analyzed_at')->nullable()->after('analysis');
        });

        // Restore analysis columns to job_applications table
        Schema::table('job_applications', function (Blueprint $table) {
            $table->json('role_analysis')->nullable()->after('notes');
            $table->json('profile_matching')->nullable()->after('role_analysis');
            $table->json('cover_letter')->nullable()->after('profile_matching');
        });
    }
};
