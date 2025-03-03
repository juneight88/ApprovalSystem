@extends('layouts.app')

@section('title', 'Subject Coordinator Dashboard')
@section('nav_title', 'Subject Coordinator Dashboard')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex flex-column align-items-center gap-3">
                <!-- File a Request Dropdown -->
                <div class="dropdown w-100">
                    <button class="btn btn-primary w-100 py-3 d-flex justify-content-between align-items-center" 
                            type="button" 
                            id="requestDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <span class="fs-5">FILE A REQUEST</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu w-100" aria-labelledby="requestDropdown">
                        <li>
                            <a class="dropdown-item py-3" href="{{ route('request.form') }}">
                                <i class="fas fa-file-alt me-2"></i>TEST QUESTIONNAIRE
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-3" href="{{ route('request.form.doc') }}">
                                <i class="fas fa-file-medical me-2"></i>DOCUMENT
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Request History Button -->
                <a href="{{ route('request.history') }}" 
                   class="btn btn-outline-primary w-100 py-3">
                    <span class="fs-5">REQUEST HISTORY</span>
                </a>

                <!-- Manage Request Button -->
                <a href="{{ url('/manage-requests') }}" 
                   class="btn btn-outline-primary w-100 py-3">
                    <span class="fs-5">MANAGE REQUEST</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.btn {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.dropdown-menu {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-top: 0.5rem;
}

.dropdown-item {
    transition: background-color 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>
@endsection