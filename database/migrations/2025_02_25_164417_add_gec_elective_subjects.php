<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add GEC Elective subjects
        DB::table('subjects')->insert([
            [
                'code' => 'GEC-E1-IT',
                'name' => 'Living in the IT Era',
                'education_level' => 'COLLEGE',
                'department' => 'CAS',
                'program' => 'GEC-ELECTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'GEC-E1-ENV',
                'name' => 'Environmental Science',
                'education_level' => 'COLLEGE',
                'department' => 'CAS',
                'program' => 'GEC-ELECTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        DB::table('subjects')
            ->whereIn('code', ['GEC-E1-IT', 'GEC-E1-ENV'])
            ->delete();
    }
};
