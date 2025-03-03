<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Subject;

class SubjectsSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // CCIS
            [
                'name' => 'BSIT Subjects',
                'code' => 'CCIS-BSIT-SUB',
                'department' => 'CCIS',
                'program' => 'BSIT',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSCS Subjects',
                'code' => 'CCIS-BSCS-SUB',
                'department' => 'CCIS',
                'program' => 'BSCS',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSIS Subjects',
                'code' => 'CCIS-BSIS-SUB',
                'department' => 'CCIS',
                'program' => 'BSIS',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BLIS Subjects',
                'code' => 'CCIS-BLIS-SUB',
                'department' => 'CCIS',
                'program' => 'BLIS',
                'education_level' => 'COLLEGE'
            ],
            
            // CTE
            [
                'name' => 'BPED Subjects',
                'code' => 'CTE-BPED-SUB',
                'department' => 'CTE',
                'program' => 'BPED',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSED-SS Subjects',
                'code' => 'CTE-BSED-SS-SUB',
                'department' => 'CTE',
                'program' => 'BSED-SS',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSED-SCIENCE Subjects',
                'code' => 'CTE-BSED-SCI-SUB',
                'department' => 'CTE',
                'program' => 'BSED-SCIENCE',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSED-MATHEMATICS Subjects',
                'code' => 'CTE-BSED-MATH-SUB',
                'department' => 'CTE',
                'program' => 'BSED-MATHEMATICS',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BTVTED Subjects',
                'code' => 'CTE-BTVTED-SUB',
                'department' => 'CTE',
                'program' => 'BTVTED',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSED-ENGLISH Subjects',
                'code' => 'CTE-BSED-ENG-SUB',
                'department' => 'CTE',
                'program' => 'BSED-ENGLISH',
                'education_level' => 'COLLEGE'
            ],
            
            // CBM
            [
                'name' => 'BSBA Subjects',
                'code' => 'CBM-BSBA-SUB',
                'department' => 'CBM',
                'program' => 'BSBA',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BPA Subjects',
                'code' => 'CBM-BPA-SUB',
                'department' => 'CBM',
                'program' => 'BPA',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSE Subjects',
                'code' => 'CBM-BSE-SUB',
                'department' => 'CBM',
                'program' => 'BSE',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSAIS Subjects',
                'code' => 'CBM-BSAIS-SUB',
                'department' => 'CBM',
                'program' => 'BSAIS',
                'education_level' => 'COLLEGE'
            ],
            
            // CCJE
            [
                'name' => 'BSCRIM Subjects',
                'code' => 'CCJE-BSCRIM-SUB',
                'department' => 'CCJE',
                'program' => 'BSCRIM',
                'education_level' => 'COLLEGE'
            ],
            
            // CTHM
            [
                'name' => 'BSTM Subjects',
                'code' => 'CTHM-BSTM-SUB',
                'department' => 'CTHM',
                'program' => 'BSTM',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'BSHM Subjects',
                'code' => 'CTHM-BSHM-SUB',
                'department' => 'CTHM',
                'program' => 'BSHM',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'SCS Subjects',
                'code' => 'CTHM-SCS-SUB',
                'department' => 'CTHM',
                'program' => 'SCS',
                'education_level' => 'COLLEGE'
            ],
            
            // CAS
            [
                'name' => 'GEC Subjects',
                'code' => 'CAS-GEC-SUB',
                'department' => 'CAS',
                'program' => 'GEC',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'GEC ELECT Subjects',
                'code' => 'CAS-GEC-ELECT-SUB',
                'department' => 'CAS',
                'program' => 'GEC ELECT',
                'education_level' => 'COLLEGE'
            ],
            [
                'name' => 'AB Subjects',
                'code' => 'CAS-AB-SUB',
                'department' => 'CAS',
                'program' => 'AB',
                'education_level' => 'COLLEGE'
            ],
            
            // Basic Education Subjects
            [
                'name' => 'ENGLISH',
                'code' => 'BE-ENG',
                'department' => 'BASIC EDUCATION',
                'program' => 'ENGLISH',
                'education_level' => 'BASIC_ED'
            ],
            [
                'name' => 'MATHEMATICS',
                'code' => 'BE-MATH',
                'department' => 'BASIC EDUCATION',
                'program' => 'MATHEMATICS',
                'education_level' => 'BASIC_ED'
            ],
            [
                'name' => 'SCIENCE',
                'code' => 'BE-SCI',
                'department' => 'BASIC EDUCATION',
                'program' => 'SCIENCE',
                'education_level' => 'BASIC_ED'
            ],
            [
                'name' => 'MAPEH',
                'code' => 'BE-MAPEH',
                'department' => 'BASIC EDUCATION',
                'program' => 'MAPEH',
                'education_level' => 'BASIC_ED'
            ],
            [
                'name' => 'FILIPINO',
                'code' => 'BE-FIL',
                'department' => 'BASIC EDUCATION',
                'program' => 'FILIPINO',
                'education_level' => 'BASIC_ED'
            ],
            [
                'name' => 'AP',
                'code' => 'BE-AP',
                'department' => 'BASIC EDUCATION',
                'program' => 'AP',
                'education_level' => 'BASIC_ED'
            ],
            [
                'name' => 'TLE',
                'code' => 'BE-TLE',
                'department' => 'BASIC EDUCATION',
                'program' => 'TLE',
                'education_level' => 'BASIC_ED'
            ],
            [
                'name' => 'VALUES EDUCATION',
                'code' => 'BE-VALUES',
                'department' => 'BASIC EDUCATION',
                'program' => 'VALUES EDUCATION',
                'education_level' => 'BASIC_ED'
            ]
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}