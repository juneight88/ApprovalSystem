<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Subject;

class AddBasicEducationSubjects extends Migration
{
    public function up()
    {
        // Elementary Subjects
        $elemSubjects = [
            'ENGLISH' => 'ENGLISH_ELEM',
            'MATHEMATICS' => 'MATHEMATICS_ELEM',
            'SCIENCE' => 'SCIENCE_ELEM',
            'MAPEH' => 'MAPEH_ELEM',
            'FILIPINO' => 'FILIPINO_ELEM',
            'AP' => 'AP_ELEM',
            'TLE' => 'TLE_ELEM',
            'GMRC' => 'GMRC_ELEM',
            'VALUES EDUCATION' => 'VALUES_EDUCATION_ELEM'
        ];

        foreach ($elemSubjects as $name => $code) {
            Subject::create([
                'name' => $name,
                'code' => $code,
                'department' => 'BASIC EDUCATION',
                'education_level' => 'ELEM',
                'program' => $name
            ]);
        }

        // Junior High School Subjects
        $jhsSubjects = [
            'ENGLISH' => 'ENGLISH_JHS',
            'MATHEMATICS' => 'MATHEMATICS_JHS',
            'SCIENCE' => 'SCIENCE_JHS',
            'MAPEH' => 'MAPEH_JHS',
            'FILIPINO' => 'FILIPINO_JHS',
            'AP' => 'AP_JHS',
            'TLE' => 'TLE_JHS',
            'VALUES EDUCATION' => 'VALUES_EDUCATION_JHS'
        ];

        foreach ($jhsSubjects as $name => $code) {
            Subject::create([
                'name' => $name,
                'code' => $code,
                'department' => 'BASIC EDUCATION',
                'education_level' => 'JHS',
                'program' => $name
            ]);
        }

        // Senior High School Subjects
        $shsSubjects = [
            'ENGLISH' => 'ENGLISH_SHS',
            'MATHEMATICS' => 'MATHEMATICS_SHS',
            'SCIENCE' => 'SCIENCE_SHS',
            'MAPEH' => 'MAPEH_SHS',
            'FILIPINO' => 'FILIPINO_SHS',
            'TLE' => 'TLE_SHS',
            'VALUES EDUCATION' => 'VALUES_EDUCATION_SHS'
        ];

        foreach ($shsSubjects as $name => $code) {
            Subject::create([
                'name' => $name,
                'code' => $code,
                'department' => 'BASIC EDUCATION',
                'education_level' => 'SHS',
                'program' => $name
            ]);
        }
    }

    public function down()
    {
        Subject::where('department', 'BASIC EDUCATION')->delete();
    }
}
