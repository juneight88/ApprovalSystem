<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Questionnaire Request Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel ="stylesheet" href="{{asset('css/form.css')}}">
    <style>
        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            padding: 20px;
        }
        
        .form-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        /* Subject dropdown styling */
        #subject_id {
            font-size: 14px;
        }

        #subject_id optgroup {
            font-weight: bold;
            color: #333;
        }

        #subject_id optgroup.font-weight-bold {
            font-size: 15px;
            color: #000;
            background-color: #f8f9fa;
        }

        #subject_id optgroup.font-weight-normal {
            font-weight: normal;
            padding-left: 15px;
        }

        #subject_id option {
            padding: 5px 10px;
        }

        .btn-custom {
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }

        .btn-submit {
            background-color: #0d6efd;
            color: white;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .file-upload {
            margin-bottom: 20px;
        }

        .alert {
            border-radius: 8px;
        }
        
        select optgroup.department-label {
            font-weight: bold;
            color: #000;
            background-color: #f8f9fa;
            font-size: 14px;
        }
        
        select option {
            padding: 8px;
            margin: 2px 0;
        }
        
        .form-select {
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h3 class="text-center mb-4">Test Questionnaire Request Form</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('submit.request') }}" method="POST" enctype="multipart/form-data">
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
                    <option value="Prelim">Prelim</option>
                    <option value="Midterm">Midterm</option>
                    <option value="Finals">Finals</option>
                    <option value="Quiz">Quiz</option>
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
                <input type="date" class="form-control" name="date_exam" required min="{{ date('Y-m-d') }}">
            </div>
        </div>

        <!-- Modality and Subject -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="modality_of_learning" class="form-label">Modality of Learning:</label>
                <select class="form-select" name="modality_of_learning" required>
                    <option value="">Select Modality</option>
                    <option value="Face-to-face">Face-to-face</option>
                    <option value="Online">Online</option>
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
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </optgroup>
                        
                        <!-- Junior High School -->
                        <optgroup label="Junior High School (JHS) Subjects">
                            @foreach($subjects->where('department', 'BASIC EDUCATION')->where('education_level', 'JHS')->sortBy('name') as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </optgroup>

                        <!-- Senior High School -->
                        <optgroup label="Senior High School (SHS) Subjects">
                            @foreach($subjects->where('department', 'BASIC EDUCATION')->where('education_level', 'SHS')->sortBy('name') as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </optgroup>
                    </optgroup>

                    <!-- College Departments -->
                    <!-- CCIS -->
                    <optgroup label="CCIS Department" class="department-label">
                        @foreach($subjects->where('department', 'CCIS')->where('program', '!=', 'GEC')->sortBy('name') as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </optgroup>
                    
                    <!-- CTE -->
                    <optgroup label="CTE Department" class="department-label">
                        @foreach($subjects->where('department', 'CTE')->where('program', '!=', 'GEC')->sortBy('name') as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </optgroup>
                    
                    <!-- CAS -->
                    <optgroup label="CAS Department" class="department-label">
                        @foreach($subjects->where('department', 'CAS')->where('program', '!=', 'GEC')->sortBy('name') as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </optgroup>
                    
                    <!-- CCJE -->
                    <optgroup label="CCJE Department" class="department-label">
                        @foreach($subjects->where('department', 'CCJE')->where('program', '!=', 'GEC')->sortBy('name') as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </optgroup>
                    
                    <!-- CBM -->
                    <optgroup label="CBM Department" class="department-label">
                        @foreach($subjects->where('department', 'CBM')->where('program', '!=', 'GEC')->sortBy('name') as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </optgroup>
                    
                    <!-- CTHM -->
                    <optgroup label="CTHM Department" class="department-label">
                        @foreach($subjects->where('department', 'CTHM')->where('program', '!=', 'GEC')->sortBy('name') as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </optgroup>

                    <!-- General Education -->
                    <optgroup label="General Education Courses (GEC)" class="department-label">
                        @foreach($subjects->where('program', 'GEC')->sortBy('code') as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                        @endforeach
                    </optgroup>

                    <!-- GEC Electives -->
                    <optgroup label="GEC Elective Courses" class="department-label">
                        @foreach($subjects->where('program', 'GEC-ELECTIVE')->sortBy('code') as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
        </div>

        <!-- Specific Subject -->
        <div class="mb-3">
            <label for="specific_subject" class="form-label">Specify Subject (if not listed above):</label>
            <input type="text" class="form-control" name="specific_subject">
        </div>

        <!-- Time Allotment and Paper Size -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="exam_time_allotment" class="form-label">Exam Time Allotment (minutes):</label>
                <input type="number" class="form-control" name="exam_time_allotment" required min="1">
            </div>
            <div class="col-md-6">
                <label for="paper_size" class="form-label">Paper Size:</label>
                <select class="form-select" name="paper_size" required>
                    <option value="">Select Paper Size</option>
                    <option value="Legal">Legal</option>
                    <option value="Short">Short</option>
                    <option value="A4">A4</option>
                </select>
            </div>
        </div>

        <!-- Printing Mode and Number of Pages -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="printing_mode" class="form-label">Printing Mode:</label>
                <select class="form-select" name="printing_mode" required>
                    <option value="">Select Printing Mode</option>
                    <option value="Both sides">Print on both sides</option>
                    <option value="One side only">Print on one side only</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="number_of_pages" class="form-label">Number of Pages:</label>
                <input type="number" class="form-control" name="number_of_pages" required min="1">
            </div>
        </div>

        <!-- Number of Copies -->
        <div class="mb-3">
            <label for="number_of_copies" class="form-label">Number of Copies:</label>
            <input type="number" class="form-control" name="number_of_copies" required min="1">
        </div>

        <!-- File Upload -->
        <div class="mb-4">
            <label for="file" class="form-label">Attach File (PDF only):</label>
            <input type="file" class="form-control" name="file" accept="application/pdf">
            <small class="text-muted">Maximum file size: 2MB</small>
        </div>

        <!-- Form Buttons -->
        <div class="d-flex justify-content-between">
            <button type="reset" class="btn btn-cancel btn-custom">Clear Form</button>
            <button type="submit" class="btn btn-submit btn-custom">Submit Request</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for exam date to today
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="date_exam"]').min = today;

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        const fileInput = document.querySelector('input[name="file"]');
        if (fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size / 1024 / 1024; // Convert to MB
            if (fileSize > 2) {
                event.preventDefault();
                alert('File size must not exceed 2MB');
            }
        }
    });
});
</script>

</body>
</html>