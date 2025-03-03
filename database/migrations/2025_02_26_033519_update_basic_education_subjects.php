<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBasicEducationSubjects extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Update education_level to be more specific
            $table->string('education_level')->change(); // Make sure it can hold longer values
            
            // Add new columns for Basic Education
            $table->string('subject_name')->nullable()->after('name');
            $table->string('subject_code')->nullable()->after('code');
        });

        // Insert Basic Education subjects
        $subjects = [
            // English subjects for each level
            ['name' => 'ENGLISH_ELEM', 'subject_name' => 'ENGLISH', 'education_level' => 'ELEMENTARY', 'department' => 'BASIC EDUCATION', 'program' => 'ELEMENTARY', 'code' => 'ENG_ELEM', 'subject_code' => 'ENGLISH'],
            ['name' => 'ENGLISH_JHS', 'subject_name' => 'ENGLISH', 'education_level' => 'JUNIOR HIGH SCHOOL', 'department' => 'BASIC EDUCATION', 'program' => 'JUNIOR HIGH SCHOOL', 'code' => 'ENG_JHS', 'subject_code' => 'ENGLISH'],
            ['name' => 'ENGLISH_SHS', 'subject_name' => 'ENGLISH', 'education_level' => 'SENIOR HIGH SCHOOL', 'department' => 'BASIC EDUCATION', 'program' => 'SENIOR HIGH SCHOOL', 'code' => 'ENG_SHS', 'subject_code' => 'ENGLISH'],
            
            // Math subjects for each level
            ['name' => 'MATHEMATICS_ELEM', 'subject_name' => 'MATHEMATICS', 'education_level' => 'ELEMENTARY', 'department' => 'BASIC EDUCATION', 'program' => 'ELEMENTARY', 'code' => 'MATH_ELEM', 'subject_code' => 'MATHEMATICS'],
            ['name' => 'MATHEMATICS_JHS', 'subject_name' => 'MATHEMATICS', 'education_level' => 'JUNIOR HIGH SCHOOL', 'department' => 'BASIC EDUCATION', 'program' => 'JUNIOR HIGH SCHOOL', 'code' => 'MATH_JHS', 'subject_code' => 'MATHEMATICS'],
            ['name' => 'MATHEMATICS_SHS', 'subject_name' => 'MATHEMATICS', 'education_level' => 'SENIOR HIGH SCHOOL', 'department' => 'BASIC EDUCATION', 'program' => 'SENIOR HIGH SCHOOL', 'code' => 'MATH_SHS', 'subject_code' => 'MATHEMATICS'],
            
            // Science subjects for each level
            ['name' => 'SCIENCE_ELEM', 'subject_name' => 'SCIENCE', 'education_level' => 'ELEMENTARY', 'department' => 'BASIC EDUCATION', 'program' => 'ELEMENTARY', 'code' => 'SCI_ELEM', 'subject_code' => 'SCIENCE'],
            ['name' => 'SCIENCE_JHS', 'subject_name' => 'SCIENCE', 'education_level' => 'JUNIOR HIGH SCHOOL', 'department' => 'BASIC EDUCATION', 'program' => 'JUNIOR HIGH SCHOOL', 'code' => 'SCI_JHS', 'subject_code' => 'SCIENCE'],
            ['name' => 'SCIENCE_SHS', 'subject_name' => 'SCIENCE', 'education_level' => 'SENIOR HIGH SCHOOL', 'department' => 'BASIC EDUCATION', 'program' => 'SENIOR HIGH SCHOOL', 'code' => 'SCI_SHS', 'subject_code' => 'SCIENCE'],
            
            // Add other subjects following the same pattern...
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['name' => $subject['name']],
                $subject
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('subject_name');
            $table->dropColumn('subject_code');
        });
    }
}
