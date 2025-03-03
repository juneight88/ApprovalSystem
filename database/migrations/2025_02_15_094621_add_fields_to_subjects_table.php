<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'category')) {
                $table->string('category')->after('name')->nullable();
            }
            if (!Schema::hasColumn('subjects', 'department')) {
                $table->string('department')->after('category')->nullable();
            }
            if (!Schema::hasColumn('subjects', 'program')) {
                $table->string('program')->after('department')->nullable();
            }
            if (!Schema::hasColumn('subjects', 'code')) {
                $table->string('code')->after('program')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['category', 'department', 'program', 'code']);
        });
    }
};
