@extends('layouts.app')

@section('title', 'Edit Request')
@section('nav_title', 'Edit Rejected Request')

@section('content')
<div class="container">
    <div class="form-container">
        <h3 class="text-center mb-4">Edit Rejected Request</h3>

        @if($request->head_office_comment)
            <div class="alert alert-info">
                <strong>Head Office Comment:</strong> {{ $request->head_office_comment }}
            </div>
        @endif

        @if($request->coordinator_comment)
            <div class="alert alert-info">
                <strong>Coordinator Comment:</strong> {{ $request->coordinator_comment }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('request.resubmit', $request->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Department (Pre-selected based on profile) -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="department" class="form-label">Department:</label>
                    <input type="text" class="form-control" name="department" value="{{ $user->department }}" readonly>
                </div>
                
                <!-- Test Category -->
                <div class="col-md-6">
                    <label for="test_category" class="form-label">Test Category:</label>
                    <select class="form-select" name="test_category" required>
                        <option value="">Select Test Category</option>
                        <option value="Prelim" {{ $request->test_category == 'Prelim' ? 'selected' : '' }}>Prelim</option>
                        <option value="Midterm" {{ $request->test_category == 'Midterm' ? 'selected' : '' }}>Midterm</option>
                        <option value="Finals" {{ $request->test_category == 'Finals' ? 'selected' : '' }}>Finals</option>
                        <option value="Quiz" {{ $request->test_category == 'Quiz' ? 'selected' : '' }}>Quiz</option>
                    </select>
                </div>
            </div>

            <!-- Date Request and Date of Exam -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="date_request" class="form-label">Date Request:</label>
                    <input type="date" class="form-control" name="date_request" value="{{ date('Y-m-d') }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="date_exam" class="form-label">Date of Exam:</label>
                    <input type="date" class="form-control" name="date_exam" required min="{{ date('Y-m-d') }}" value="{{ $request->date_exam ? date('Y-m-d', strtotime($request->date_exam)) : '' }}">
                </div>
            </div>

            <!-- Modality and Subject -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="modality_of_learning" class="form-label">Modality of Learning:</label>
                    <select class="form-select" name="modality_of_learning" required>
                        <option value="">Select Modality</option>
                        <option value="Face-to-face" {{ $request->modality_of_learning == 'Face-to-face' ? 'selected' : '' }}>Face-to-face</option>
                        <option value="Online" {{ $request->modality_of_learning == 'Online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="subject_id" class="form-label">Subject:</label>
                    <select class="form-select" id="subject_id" name="subject_id" required>
                        <option value="">Select a Subject</option>
                        
                        <!-- Basic Education Department -->
                        <optgroup label="Basic Education Department" class="department-label">
                            <!-- Elementary -->
                            <optgroup label="Elementary (Elem) Subjects">
                                @foreach($subjects->where('department', 'BASIC EDUCATION')->where('education_level', 'ELEM')->sortBy('name') as $subject)
                                    <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                            
                            <!-- Junior High School -->
                            <optgroup label="Junior High School (JHS) Subjects">
                                @foreach($subjects->where('department', 'BASIC EDUCATION')->where('education_level', 'JHS')->sortBy('name') as $subject)
                                    <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </optgroup>

                            <!-- Senior High School -->
                            <optgroup label="Senior High School (SHS) Subjects">
                                @foreach($subjects->where('department', 'BASIC EDUCATION')->where('education_level', 'SHS')->sortBy('name') as $subject)
                                    <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </optgroup>

                        <!-- College Departments -->
                        <!-- CCIS -->
                        <optgroup label="CCIS Department" class="department-label">
                            @foreach($subjects->where('department', 'CCIS')->where('program', '!=', 'GEC')->sortBy('name') as $subject)
                                <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->code }} - {{ $subject->name }}
                                </option>
                            @endforeach
                        </optgroup>

                        <!-- GEC Subjects -->
                        <optgroup label="General Education Courses" class="department-label">
                            @foreach($subjects->where('program', 'GEC')->sortBy('code') as $subject)
                                <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->code }} - {{ $subject->name }}
                                </option>
                            @endforeach
                        </optgroup>

                        <!-- GEC Elective Subjects -->
                        <optgroup label="GEC Elective Courses" class="department-label">
                            @foreach($subjects->where('program', 'GEC-ELECTIVE')->sortBy('code') as $subject)
                                <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->code }} - {{ $subject->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- Specific Subject -->
            <div class="mb-3">
                <label for="specific_subject" class="form-label">Specify Subject (if not listed above):</label>
                <input type="text" class="form-control" name="specific_subject" value="{{ $request->specific_subject }}">
            </div>

            <!-- Time Allotment -->
            <div class="mb-3">
                <label for="exam_time_allotment" class="form-label">Time Allotment (in minutes):</label>
                <input type="number" class="form-control" name="exam_time_allotment" required value="{{ $request->exam_time_allotment }}">
            </div>

            <!-- Paper Size and Printing Mode -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="paper_size" class="form-label">Paper Size:</label>
                    <select class="form-select" name="paper_size" required>
                        <option value="">Select Paper Size</option>
                        <option value="Legal" {{ $request->paper_size == 'Legal' ? 'selected' : '' }}>Legal</option>
                        <option value="Short" {{ $request->paper_size == 'Short' ? 'selected' : '' }}>Short</option>
                        <option value="A4" {{ $request->paper_size == 'A4' ? 'selected' : '' }}>A4</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="printing_mode" class="form-label">Printing Mode:</label>
                    <select class="form-select" name="printing_mode" required>
                        <option value="">Select Printing Mode</option>
                        <option value="Both sides" {{ $request->printing_mode == 'Both sides' ? 'selected' : '' }}>Both sides</option>
                        <option value="One side only" {{ $request->printing_mode == 'One side only' ? 'selected' : '' }}>One side only</option>
                    </select>
                </div>
            </div>

            <!-- Number of Pages and Copies -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="number_of_pages" class="form-label">Number of Pages:</label>
                    <input type="number" class="form-control" name="number_of_pages" required value="{{ $request->number_of_pages }}">
                </div>
                <div class="col-md-6">
                    <label for="number_of_copies" class="form-label">Number of Copies:</label>
                    <input type="number" class="form-control" name="number_of_copies" required value="{{ $request->number_of_copies }}">
                </div>
            </div>

            <!-- File Upload -->
            <div class="mb-4">
                <label for="file" class="form-label">Upload File (PDF only, max 2MB):</label>
                <input type="file" class="form-control" name="file" accept=".pdf">
                @if($request->file_path)
                    <div class="mt-2">
                        <span class="text-muted">Current file:</span>
                        <a href="{{ asset('storage/' . $request->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-file-pdf"></i> View Current File
                        </a>
                    </div>
                @endif
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <a href="{{ route('request.history') }}" class="btn btn-secondary btn-custom me-2">Cancel</a>
                <button type="submit" class="btn btn-primary btn-custom">Resubmit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
