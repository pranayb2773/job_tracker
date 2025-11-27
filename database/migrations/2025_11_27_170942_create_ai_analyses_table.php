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
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->morphs('analyzable'); // Creates analyzable_id and analyzable_type
            $table->string('type'); // e.g., 'document', 'role_analysis', 'profile_matching', 'cover_letter'
            $table->json('data'); // The analysis result data
            $table->string('provider')->nullable(); // AI provider used (gemini, claude, openai, groq)
            $table->string('model')->nullable(); // Model used for analysis
            $table->integer('prompt_tokens')->nullable(); // Token usage for prompt
            $table->integer('completion_tokens')->nullable(); // Token usage for completion
            $table->timestamp('analyzed_at'); // When the analysis was performed
            $table->timestamps();

            // Index for better query performance
            $table->index(['analyzable_type', 'analyzable_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};
