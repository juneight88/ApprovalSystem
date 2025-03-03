<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDepartmentColumnLength extends Migration
{
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('department', 50)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('department', 50)->change();
        });
    }

    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('department', 20)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('department', 20)->change();
        });
    }
}
