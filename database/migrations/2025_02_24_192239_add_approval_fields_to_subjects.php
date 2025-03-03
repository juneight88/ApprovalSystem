<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalFieldsToSubjects extends Migration
{
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Add subject coordinator
            $table->foreignId('subject_coordinator_id')->nullable()->constrained('users')->onDelete('set null');
            // Add department head
            $table->foreignId('department_head_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['subject_coordinator_id']);
            $table->dropForeign(['department_head_id']);
            $table->dropColumn(['subject_coordinator_id', 'department_head_id']);
        });
    }
}
