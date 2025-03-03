<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, modify the column to be a string type temporarily
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->change();
        });

        // Then, modify it back to enum with updated values
        DB::statement("ALTER TABLE users MODIFY department ENUM('CCIS', 'CTE', 'CAS', 'CCJE', 'CBM', 'CTHM', 'ELEMENTARY', 'JHS', 'SHS', 'BASIC EDUCATION', 'OSAS', 'REGISTRAR', 'EDP', 'FINANCE', 'HR')");
    }

    public function down()
    {
        // First, modify the column to be a string type temporarily
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->change();
        });

        // Then, modify it back to the original enum values
        DB::statement("ALTER TABLE users MODIFY department ENUM('CCIS', 'CTE', 'CAS', 'CCJE', 'CBM', 'CTHM', 'ELEMENTARY', 'JHS', 'SHS')");
    }
};
