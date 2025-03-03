<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject_name', 
        'education_level',
        'department',
        'program',
        'code',
        'subject_coordinator_id',
        'department_head_id'
    ];

    // Constants for education levels
    const EDUCATION_LEVEL_ELEM = 'ELEMENTARY';
    const EDUCATION_LEVEL_JHS = 'JUNIOR HIGH SCHOOL';
    const EDUCATION_LEVEL_SHS = 'SENIOR HIGH SCHOOL';
    const EDUCATION_LEVEL_COLLEGE = 'COLLEGE';

    // Constants for Basic Education subjects
    const SUBJECT_ENGLISH = 'ENGLISH';
    const SUBJECT_MATHEMATICS = 'MATHEMATICS';
    const SUBJECT_SCIENCE = 'SCIENCE';
    const SUBJECT_MAPEH = 'MAPEH';
    const SUBJECT_FILIPINO = 'FILIPINO';
    const SUBJECT_AP = 'AP';
    const SUBJECT_TLE = 'TLE';
    const SUBJECT_ICT = 'ICT';
    const SUBJECT_VALUES = 'VALUES EDUCATION';
    const SUBJECT_RESEARCH = 'RESEARCH';

    public function subjectCoordinator()
    {
        return $this->belongsTo(User::class, 'subject_coordinator_id');
    }

    public function departmentHead()
    {
        return $this->belongsTo(User::class, 'department_head_id');
    }

    public function getSubjectGroup()
    {
        // For basic education subjects
        if ($this->department === 'BASIC EDUCATION') {
            return [
                'department' => 'BASIC EDUCATION',
                'subject_name' => $this->subject_name,
                'education_level' => $this->education_level
            ];
        }
        
        // For college subjects
        $department = $this->department;
        $program = null;
        
        switch($this->department) {
            case 'CCIS':
                switch($this->program) {
                    case 'BSIT':
                        $program = 'BSIT Subjects';
                        break;
                    case 'BSCS':
                        $program = 'BSCS Subjects';
                        break;
                    case 'BSIS':
                        $program = 'BSIS Subjects';
                        break;
                    case 'BLIS':
                        $program = 'BLIS Subjects';
                        break;
                }
                break;
            case 'CTE':
                switch($this->program) {
                    case 'BPED':
                        $program = 'BPED Subjects';
                        break;
                    case 'BSED-SS':
                        $program = 'BSED-SS Subjects';
                        break;
                    case 'BSED-SCIENCE':
                        $program = 'BSED-SCIENCE Subjects';
                        break;
                    case 'BSED-MATHEMATICS':
                        $program = 'BSED-MATHEMATICS Subjects';
                        break;
                    case 'BTVTED':
                        $program = 'BTVTED Subjects';
                        break;
                    case 'BSED-ENGLISH':
                        $program = 'BSED-ENGLISH Subjects';
                        break;
                }
                break;
            case 'CBM':
                switch($this->program) {
                    case 'BSBA':
                        $program = 'BSBA Subjects';
                        break;
                    case 'BPA':
                        $program = 'BPA Subjects';
                        break;
                    case 'BSE':
                        $program = 'BSE Subjects';
                        break;
                    case 'BSAIS':
                        $program = 'BSAIS Subjects';
                        break;
                }
                break;
            case 'CCJE':
                if ($this->program === 'BSCRIM') {
                    $program = 'BSCRIM Subjects';
                }
                break;
            case 'CTHM':
                switch($this->program) {
                    case 'BSTM':
                        $program = 'BSTM Subjects';
                        break;
                    case 'BSHM':
                        $program = 'BSHM Subjects';
                        break;
                    case 'SCS':
                        $program = 'SCS Subjects';
                        break;
                }
                break;
            case 'CAS':
                switch($this->program) {
                    case 'GEC':
                        $program = 'GEC Subjects';
                        break;
                    case 'AB':
                        $program = 'AB Subjects';
                        break;
                    case 'GEC ELECT':
                        $program = 'GEC ELECT Subjects';
                        break;
                }
                break;
        }
        
        return [
            'department' => $department,
            'program' => $program
        ];
    }

    public function isBasicEducation()
    {
        return $this->department === 'BASIC EDUCATION';
    }

    public function getSubjectsByEducationLevel($level)
    {
        return self::where('education_level', $level)->get();
    }
}
