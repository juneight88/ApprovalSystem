<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Request Approval System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Layout Styles */
        .wrapper {
            display: flex;
            min-height: calc(100vh - 56px); /* Subtract navbar height */
        }
        .sidebar {
            width: 280px;
            background: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
            overflow-y: auto;
        }
        
        /* Common Styles */
        .dashboard-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #0d6efd;
        }
        .stats-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
        }
        .stats-label {
            color: #666;
            font-size: 1rem;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 500;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 600;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }
        .btn-sm {
            padding: 5px 10px;
        }
        .table th {
            font-weight: 600;
            color: #333;
        }
        .comment-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 10px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
    @yield('additional_css')
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">@yield('nav_title', 'Request Approval System')</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if(Session::has('user'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/logout') }}">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="wrapper">
        <!-- Sidebar with Profile -->
        @if(Session::has('user'))
        <div class="sidebar">
            @include('layouts.profile-card')
        </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
