<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalTimestampsToRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->timestamp('coordinator_approved_at')->nullable();
            $table->timestamp('head_office_approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->foreign('rejected_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropColumn('coordinator_approved_at');
            $table->dropColumn('head_office_approved_at');
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejected_by');
        });
    }
}
