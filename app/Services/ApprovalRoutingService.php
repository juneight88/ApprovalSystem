<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\User;
use App\Models\Request;

class ApprovalRoutingService
{
    public function determineApprovers(Subject $subject, User $requestor)
    {
        // Default approvers
        $approvers = [
            'coordinator' => null,
            'head_office' => null
        ];

        // Log the request details
        \Log::info('Determining approvers for request', [
            'subject' => [
                'name' => $subject->name,
                'department' => $subject->department,
                'education_level' => $subject->education_level
            ],
            'requestor' => [
                'id' => $requestor->id,
                'department' => $requestor->department
            ]
        ]);

        // Handle Basic Education subjects
        if ($subject->department === 'BASIC EDUCATION') {
            // Find the subject coordinator who handles this subject across all levels
            $coordinator = User::where('role', 'Subject Coordinator')
                ->where('department', 'BASIC EDUCATION')
                ->where(function($query) use ($subject) {
                    $query->whereJsonContains('subject_handled', $subject->name)
                          ->orWhere('subject_handled', 'like', '%"' . $subject->name . '"%')
                          ->orWhere('subject_handled', $subject->name);
                })
                ->first();

            if ($coordinator) {
                $approvers['coordinator'] = $coordinator;
                
                \Log::info('Found Basic Education Subject Coordinator', [
                    'coordinator_id' => $coordinator->id,
                    'coordinator_name' => $coordinator->first_name . ' ' . $coordinator->last_name,
                    'subject_handled' => $coordinator->subject_handled,
                    'for_subject' => $subject->name
                ]);
            } else {
                \Log::warning('No Subject Coordinator found for Basic Education subject', [
                    'subject_name' => $subject->name,
                    'education_level' => $subject->education_level
                ]);
            }

            // Map education levels to Head of Office roles
            $levelMap = [
                'ELEMENTARY' => 'Head of Office',
                'JUNIOR HIGH SCHOOL' => 'Head of Office',
                'SENIOR HIGH SCHOOL' => 'Head of Office',
                // Add short form mappings
                'ELEM' => 'Head of Office',
                'JHS' => 'Head of Office',
                'SHS' => 'Head of Office'
            ];

            // Normalize the education level to uppercase for consistent comparison
            $normalizedLevel = strtoupper($subject->education_level);
            
            // Map short forms to full forms
            switch($normalizedLevel) {
                case 'ELEM':
                    $normalizedLevel = 'ELEMENTARY';
                    break;
                case 'JHS':
                    $normalizedLevel = 'JUNIOR HIGH SCHOOL';
                    break;
                case 'SHS':
                    $normalizedLevel = 'SENIOR HIGH SCHOOL';
                    break;
            }
            
            // Use array access instead of null coalescing operator for PHP 7.x compatibility
            $headOfOfficeTitle = isset($levelMap[$normalizedLevel]) ? $levelMap[$normalizedLevel] : null;

            if ($headOfOfficeTitle) {
                // Find the appropriate Head of Office based on education level
                $headOfOffice = User::where('role', $headOfOfficeTitle)
                    ->where('department', 'BASIC EDUCATION')
                    ->where(function($query) use ($normalizedLevel) {
                        $shortForm = '';
                        switch($normalizedLevel) {
                            case 'ELEMENTARY':
                                $shortForm = 'ELEM';
                                break;
                            case 'JUNIOR HIGH SCHOOL':
                                $shortForm = 'JHS';
                                break;
                            case 'SENIOR HIGH SCHOOL':
                                $shortForm = 'SHS';
                                break;
                            default:
                                $shortForm = $normalizedLevel;
                        }
                        $query->where('program', $normalizedLevel)
                              ->orWhere('program', $shortForm);
                    })
                    ->first();

                if ($headOfOffice) {
                    $approvers['head_office'] = $headOfOffice;
                    
                    \Log::info('Found Basic Education Head of Office', [
                        'head_id' => $headOfOffice->id,
                        'head_name' => $headOfOffice->first_name . ' ' . $headOfOffice->last_name,
                        'role' => $headOfOffice->role,
                        'education_level' => $normalizedLevel,
                        'program' => $headOfOffice->program
                    ]);
                } else {
                    $shortForm = '';
                    switch($normalizedLevel) {
                        case 'ELEMENTARY':
                            $shortForm = 'ELEM';
                            break;
                        case 'JUNIOR HIGH SCHOOL':
                            $shortForm = 'JHS';
                            break;
                        case 'SENIOR HIGH SCHOOL':
                            $shortForm = 'SHS';
                            break;
                        default:
                            $shortForm = $normalizedLevel;
                    }
                    \Log::warning('No Head of Office found for education level', [
                        'education_level' => $normalizedLevel,
                        'expected_role' => $headOfOfficeTitle,
                        'program' => $shortForm
                    ]);
                }
            } else {
                \Log::error('Invalid education level for Basic Education subject', [
                    'subject_name' => $subject->name,
                    'education_level' => $normalizedLevel
                ]);
            }

            // Validate the complete approval route
            $this->validateBasicEducationApprovalRoute($subject, $approvers);
            
            return $approvers;
        }

        // Check if it's a GEC Elective subject
        if ($subject->program === 'GEC-ELECTIVE') {
            switch ($subject->code) {
                case 'GEC-E1-IT':
                    // For "Living in the IT Era" - BSIT Subject Coordinator
                    $approvers['coordinator'] = User::where('role', 'Subject Coordinator')
                        ->where('department', 'CCIS')
                        ->where('program', 'BSIT')
                        ->first();
                    break;
                    
                case 'GEC-E1-ENV':
                    // For "Environmental Science" - BSED-Science Subject Coordinator
                    $approvers['coordinator'] = User::where('role', 'Subject Coordinator')
                        ->where('department', 'CTE')
                        ->where('program', 'BSED-SCIENCE')
                        ->first();
                    break;
            }
            
            // Both GEC Electives need CAS Head of Office approval
            $approvers['head_office'] = User::where('role', 'Head of Office')
                ->where('department', 'CAS')
                ->first();
        } 
        // Special case for regular GEC subjects
        else if ($this->isGECSubject($subject)) {
            // Get BSED-SS Subject Coordinator from CTE Department
            $approvers['coordinator'] = User::where('role', 'Subject Coordinator')
                ->where('department', 'CTE')
                ->where('program', 'BSED-SS')
                ->first();

            // Get CAS Department Head
            $approvers['head_office'] = User::where('role', 'Head of Office')
                ->where('department', 'CAS')
                ->first();
        }
        // Regular case for non-GEC subjects
        else {
            // Get coordinator based on subject's department and program
            $approvers['coordinator'] = User::where('role', 'Subject Coordinator')
                ->where('department', $subject->department)
                ->where('program', $subject->program)
                ->first();

            // Get head of office based on subject's department
            $approvers['head_office'] = User::where('role', 'Head of Office')
                ->where('department', $subject->department)
                ->first();
        }

        return $approvers;
    }

    /**
     * Validate the approval route for Basic Education subjects
     */
    private function validateBasicEducationApprovalRoute(Subject $subject, array $approvers)
    {
        $issues = [];
        $normalizedLevel = strtoupper($subject->education_level);

        if (!$approvers['coordinator']) {
            $issues[] = "No Subject Coordinator found for {$subject->name}";
        }

        if (!$approvers['head_office']) {
            $issues[] = "No Head of Office found for education level {$normalizedLevel}";
        }

        if (!in_array($normalizedLevel, ['ELEMENTARY', 'JUNIOR HIGH SCHOOL', 'SENIOR HIGH SCHOOL'])) {
            $issues[] = "Invalid education level: {$normalizedLevel}";
        }

        if ($approvers['head_office']) {
            $expectedRole = '';
            switch ($normalizedLevel) {
                case 'ELEMENTARY':
                    $expectedRole = 'Head of Office';
                    break;
                case 'JUNIOR HIGH SCHOOL':
                    $expectedRole = 'Head of Office';
                    break;
                case 'SENIOR HIGH SCHOOL':
                    $expectedRole = 'Head of Office';
                    break;
                default:
                    $expectedRole = 'Invalid education level';
            }

            if ($approvers['head_office']->role !== $expectedRole) {
                $issues[] = "Head of Office role mismatch. Expected: {$expectedRole}, Got: {$approvers['head_office']->role}";
            }
        }

        if (!empty($issues)) {
            \Log::warning('Basic Education approval route validation issues', [
                'subject' => [
                    'name' => $subject->name,
                    'education_level' => $normalizedLevel
                ],
                'issues' => $issues
            ]);
        }
    }

    private function isGECSubject(Subject $subject)
    {
        // Check if the subject code starts with 'GEC' but is not a GEC elective
        return str_starts_with($subject->code, 'GEC') && $subject->program !== 'GEC-ELECTIVE';
    }
}
