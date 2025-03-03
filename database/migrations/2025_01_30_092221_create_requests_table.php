<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('department');  // Pre-selected based on profile
            $table->enum('test_category', ['Prelim', 'Midterm', 'Finals', 'Quiz']);
            $table->date('date_request')->useCurrent();  // Auto-generated today's date
            $table->date('date_exam');  // User input
            $table->enum('modality_of_learning', ['Face-to-face', 'Online']);
            $table->foreignId('subject_id')->constrained('subjects');  // Reference to subjects table
            $table->string('specific_subject')->nullable();  // Text input if subject not listed
            $table->integer('exam_time_allotment');  // In minutes
            $table->enum('paper_size', ['Legal', 'Short', 'A4']);
            $table->enum('printing_mode', ['Both sides', 'One side only']);
            $table->integer('number_of_pages');
            $table->integer('number_of_copies');
            $table->string('file_path')->nullable();  // PDF file attachment
            
            // Approval workflow fields
            $table->enum('status', [
                'pending',
                'coordinator_approved',
                'coordinator_rejected',
                'head_approved',
                'head_rejected',
                'completed'
            ])->default('pending');
            $table->text('comments')->nullable();
            $table->timestamp('commented_at')->nullable();
            $table->foreignId('commented_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('producer')->nullable(); // Who will produce the test (admin/student assistant)
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('dean_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
