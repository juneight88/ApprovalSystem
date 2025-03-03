<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile Setup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        /* Minimal and professional design adjustments */
        body {
            position: relative;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset('images/smcc.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(8px); /* Apply blur effect */
            -webkit-filter: blur(8px); /* Apply blur effect for Safari */
            z-index: -1;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            border-radius: 8px; /* Rounded corners */
            width: 80%; /* Reduce width to show more background */
            max-width: 600px; /* Limit max width for better visibility */
            margin: 0 auto; /* Center the card */
        }
        h2 {
            color: #333; /* Darker text for contrast */
            font-weight: 600;
        }
        label {
            font-weight: 500;
            color: #555;
        }
        .form-control {
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ccc;
        }
        
        /* Media query for landscape orientation */
        @media (orientation: landscape) {
            .card {
                width: 60%;
                max-width: 800px;
            }
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-3 w-100 mx-2">
        <h2 class="text-center">Personal Information Setup</h2>
        <form action="/setup-profile" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Left column for names -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <input type="text" class="form-control" value="{{ $user->role }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Department</label>
                        <input type="text" class="form-control" value="{{ $user->department }}" readonly>
                    </div>
                </div>
                <!-- Right column for profile picture -->
                <div class="col-md-6 text-center">
                    <label>Profile Picture</label>
                    <div class="mb-3">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" class="profile-pic" alt="Profile Picture">
                        @else
                            <img src="https://via.placeholder.com/100" class="profile-pic" alt="Default Profile Picture">
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture">Upload Profile Picture (PNG, JPG, JPEG)</label>
                        <input type="file" name="profile_picture" class="form-control" accept="image/png, image/jpg, image/jpeg">
                    </div>
                    <div class="mb-3">
                        <label for="signature">Signature (PNG)</label>
                        <input type="file" name="signature" class="form-control" accept="image/png">
                    </div>
                </div>
            </div>
            <!-- Center the button -->
            <div class="form-submit">
                <button type="submit" class="btn btn-primary w-100">Save Profile</button>
            </div>
        </form>
    </div>
</body>
</html>
