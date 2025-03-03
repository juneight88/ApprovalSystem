<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            if (!Schema::hasColumn('requests', 'head_office_id')) {
                $table->unsignedBigInteger('head_office_id')->nullable()->after('coordinator_id');
                $table->foreign('head_office_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('requests', 'coordinator_comment')) {
                $table->string('coordinator_comment')->nullable()->after('head_office_id');
            }
            if (!Schema::hasColumn('requests', 'head_office_comment')) {
                $table->string('head_office_comment')->nullable()->after('coordinator_comment');
            }
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['head_office_id']);
            $table->dropColumn(['head_office_id', 'coordinator_comment', 'head_office_comment']);
        });
    }
};
