<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSubjectsTable extends Migration
{
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();  // Subject code
            $table->string('name');           // Subject name
            $table->enum('education_level', ['COLLEGE', 'BASIC_ED']); // To differentiate between college and basic education
            $table->enum('department', ['CCIS', 'CTE', 'CAS', 'CCJE', 'CBM', 'CTHM', 'ELEMENTARY', 'JHS', 'SHS']); 
            $table->string('program')->nullable(); // For college programs (BSIT, BSCS, etc.) or basic ed subjects (ENGLISH, MATH, etc.)
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Seed the default college programs
        $collegePrograms = [
            // CCIS Programs
            ['code' => 'CCIS-BSIT', 'name' => 'Bachelor of Science in Information Technology', 'education_level' => 'COLLEGE', 'department' => 'CCIS', 'program' => 'BSIT'],
            ['code' => 'CCIS-BSCS', 'name' => 'Bachelor of Science in Computer Science', 'education_level' => 'COLLEGE', 'department' => 'CCIS', 'program' => 'BSCS'],
            ['code' => 'CCIS-BSIS', 'name' => 'Bachelor of Science in Information Systems', 'education_level' => 'COLLEGE', 'department' => 'CCIS', 'program' => 'BSIS'],
            ['code' => 'CCIS-BLIS', 'name' => 'Bachelor of Library and Information Science', 'education_level' => 'COLLEGE', 'department' => 'CCIS', 'program' => 'BLIS'],
            
            // CBM Programs
            ['code' => 'CBM-BSBA', 'name' => 'Bachelor of Science in Business Administration', 'education_level' => 'COLLEGE', 'department' => 'CBM', 'program' => 'BSBA'],
            ['code' => 'CBM-BPA', 'name' => 'Bachelor of Public Administration', 'education_level' => 'COLLEGE', 'department' => 'CBM', 'program' => 'BPA/BSE'],
            ['code' => 'CBM-BSAIS', 'name' => 'BS in Accounting Information Systems', 'education_level' => 'COLLEGE', 'department' => 'CBM', 'program' => 'BSAIS'],
            
            // CAS Program
            ['code' => 'CAS-AB', 'name' => 'Bachelor of Arts', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'AB'],
            
            // GEC Subjects
            ['code' => 'GEC-101', 'name' => 'Understanding the Self', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'GEC'],
            ['code' => 'GEC-102', 'name' => 'Readings in Philippine History', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'GEC'],
            ['code' => 'GEC-103', 'name' => 'The Contemporary World', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'GEC'],
            ['code' => 'GEC-104', 'name' => 'Mathematics in the Modern World', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'GEC'],
            ['code' => 'GEC-105', 'name' => 'Purposive Communication', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'GEC'],
            ['code' => 'GEC-106', 'name' => 'Art Appreciation', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'GEC'],
            ['code' => 'GEC-107', 'name' => 'Science, Technology and Society', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'GEC'],
            ['code' => 'GEC-108', 'name' => 'Ethics', 'education_level' => 'COLLEGE', 'department' => 'CAS', 'program' => 'GEC'],
            
            // CCJE Program
            ['code' => 'CCJE-BSCRIM', 'name' => 'Bachelor of Science in Criminology', 'education_level' => 'COLLEGE', 'department' => 'CCJE', 'program' => 'BSCrim'],
            
            // CTHM Programs
            ['code' => 'CTHM-BSTM', 'name' => 'BS in Tourism Management', 'education_level' => 'COLLEGE', 'department' => 'CTHM', 'program' => 'BSTM'],
            ['code' => 'CTHM-BSHM', 'name' => 'BS in Hospitality Management', 'education_level' => 'COLLEGE', 'department' => 'CTHM', 'program' => 'BSHM'],
            ['code' => 'CTHM-SCS', 'name' => 'Short Course Services', 'education_level' => 'COLLEGE', 'department' => 'CTHM', 'program' => 'SCS'],

            // CTE Programs
            ['code' => 'CTE-BPED', 'name' => 'Bachelor in Physical Education', 'education_level' => 'COLLEGE', 'department' => 'CTE', 'program' => 'BPED'],
            ['code' => 'CTE-BSED-SS', 'name' => 'Bachelor of Secondary Education - Social Studies', 'education_level' => 'COLLEGE', 'department' => 'CTE', 'program' => 'BSED-SS'],
            ['code' => 'CTE-BSED-SCI', 'name' => 'Bachelor of Secondary Education - Science', 'education_level' => 'COLLEGE', 'department' => 'CTE', 'program' => 'BSED-SCIENCE'],
            ['code' => 'CTE-BSED-MATH', 'name' => 'Bachelor of Secondary Education - Mathematics', 'education_level' => 'COLLEGE', 'department' => 'CTE', 'program' => 'BSED-MATHEMATICS'],
            ['code' => 'CTE-BTVTED', 'name' => 'Bachelor of Technical-Vocational Teacher Education', 'education_level' => 'COLLEGE', 'department' => 'CTE', 'program' => 'BTVTED'],
            ['code' => 'CTE-BSED-ENG', 'name' => 'Bachelor of Secondary Education - English', 'education_level' => 'COLLEGE', 'department' => 'CTE', 'program' => 'BSED-ENGLISH'],
        ];

        // Seed the basic education subjects
        $basicEdSubjects = [
            // Elementary Subjects
            ['code' => 'ELEM-ENG', 'name' => 'Elementary English', 'education_level' => 'BASIC_ED', 'department' => 'ELEMENTARY', 'program' => 'ENGLISH'],
            ['code' => 'ELEM-MATH', 'name' => 'Elementary Mathematics', 'education_level' => 'BASIC_ED', 'department' => 'ELEMENTARY', 'program' => 'MATH'],
            ['code' => 'ELEM-SCI', 'name' => 'Elementary Science', 'education_level' => 'BASIC_ED', 'department' => 'ELEMENTARY', 'program' => 'SCIENCE'],
            
            // Junior High School Subjects
            ['code' => 'JHS-ENG', 'name' => 'JHS English', 'education_level' => 'BASIC_ED', 'department' => 'JHS', 'program' => 'ENGLISH'],
            ['code' => 'JHS-MATH', 'name' => 'JHS Mathematics', 'education_level' => 'BASIC_ED', 'department' => 'JHS', 'program' => 'MATH'],
            ['code' => 'JHS-SCI', 'name' => 'JHS Science', 'education_level' => 'BASIC_ED', 'department' => 'JHS', 'program' => 'SCIENCE'],
            
            // Senior High School Subjects
            ['code' => 'SHS-ENG', 'name' => 'SHS English', 'education_level' => 'BASIC_ED', 'department' => 'SHS', 'program' => 'ENGLISH'],
            ['code' => 'SHS-MATH', 'name' => 'SHS Mathematics', 'education_level' => 'BASIC_ED', 'department' => 'SHS', 'program' => 'MATH'],
            ['code' => 'SHS-SCI', 'name' => 'SHS Science', 'education_level' => 'BASIC_ED', 'department' => 'SHS', 'program' => 'SCIENCE'],
        ];

        DB::table('subjects')->insert(array_merge($collegePrograms, $basicEdSubjects));
    }

    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
