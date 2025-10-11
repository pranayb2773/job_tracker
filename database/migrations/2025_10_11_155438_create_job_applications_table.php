<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('job_title');
            $table->text('job_description')->nullable();
            $table->string('job_url')->nullable();
            $table->string('organisation');
            $table->string('source')->nullable();
            $table->string('source_url')->nullable();
            $table->string('work_arrangement')->nullable();
            $table->string('salary_range')->nullable();
            $table->integer('salary_min')->nullable();
            $table->string('status')->default('applied');
            $table->string('priority')->default('medium');
            $table->date('application_date');
            $table->date('screening_date')->nullable();
            $table->date('interview_date')->nullable();
            $table->date('technical_test_date')->nullable();
            $table->date('final_interview_date')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->date('offer_date')->nullable();
            $table->date('accepted_date')->nullable();
            $table->date('rejected_date')->nullable();
            $table->date('withdrawn_date')->nullable();
            $table->date('deadline')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
