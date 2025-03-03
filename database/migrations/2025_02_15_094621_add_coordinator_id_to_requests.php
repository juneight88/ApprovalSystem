<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            if (!Schema::hasColumn('requests', 'coordinator_id')) {
                $table->unsignedBigInteger('coordinator_id')->nullable();
                $table->foreign('coordinator_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['coordinator_id']);
            $table->dropColumn('coordinator_id');
        });
    }
};
